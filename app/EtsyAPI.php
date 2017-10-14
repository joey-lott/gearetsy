<?php

namespace App;

use GuzzleHttp\Client;
use \OAuth;

class EtsyAPI
{
    private $apiKey;
    private $secret;
    private $name;
    private $listings;
    private $nextPage = null;
    private $retrievedListings = false;


    public static $ALL_LISTINGS = "all_listings";
    public static $PAGE_LISTINGS = "page_listings";

    public function __construct($apiKey, $secret) {
      $this->apiKey = $apiKey;
      $this->secret = $secret;
    }

    public function setName($name) {
      $this->name = $name;
    }

    public function getListings() {
      return $this->listings;
    }

    public function getNextPage() {
      return $this->nextPage;
    }

    // Get the shop with $this->name as the ID/name from Etsy. Return it.
    // Can return either the shop data as an object or "404" as a string.
    // Consider refactoring to throw exception if no shop found.
    public function fetchShop($id) {
      $client = new Client;
      try {
        $results = $this->callGet("shops/".$id)->results;
        // $response = $client->get("https://openapi.etsy.com/v2/shops/".$this->name."?api_key=".$this->apiKey);
        // $results = json_decode($response->getBody())->results;
        if(count($results)) {
          // Maybe should convert to Shop object before returning.
          return $results[0];
        }
        else {
          return "404";
        }
      }
      catch(\GuzzleHttp\Exception\ClientException $error) {
        return "404";
      }
    }

    public function fetchShippingTemplates($userId) {
      $formData = [
        "user_id" => $userId,
        "limit" => "100",
        ];
        $response = $this->callOAuth("/users/".$userId."/shipping/templates", $formData, OAUTH_HTTP_METHOD_GET);
        return $response["results"];
    }

    public function fetchListings($id, $draftsBool = false, $listingEnum="page_listings") {
      $page = 1;
      $p = $this->getListingsPage($id, $page, $draftsBool);
      $results = $p->results;
      $page = $p->pagination->next_page;
      $this->nextPage = $page;
      if($listingEnum == EtsyAPI::$ALL_LISTINGS) {
        while($page) {
          $p = $this->getListingsPage($id, $page);
          $results = array_merge($results, $p->results);
          $page = $p->pagination->next_page;
        }
        $this->nextPage = null;
      }
      $this->listings = $results;
      return $results;
    }

    public function fetchCountries() {
      return $this->callGet("countries")->results;
    }

    public function fetchRegions() {
      return $this->callGet("regions")->results;
    }

    public function fetchShippingTemplateEntries($templateId) {
      $formData = [
        "shipping_template_id" => $templateId,
        "limit" => "100",
        ];
        $response = $this->callOAuth("/shipping/templates/".$templateId."/entries", $formData, OAUTH_HTTP_METHOD_GET);
        return $response["results"];
    }

    public function fetchCountryByID($countryId) {
      $response = $this->callGet("countries/".$countryId);
      dd($response);
    }

    private function getListingsPage($id, $page, $draftsBool) {
      $endpoint = "shops/".$id."/listings/";
      if($draftsBool) {
        $endpoint = $endpoint."draft";
      }
      else {
        $endpoint = $endpoint."active";
      }
      $params = "&page=".$page."&limit=100";
      if($draftsBool){
        $params = [];
        //dd($endpoint);
        return $this->callOAuth($endpoint, $params, OAUTH_HTTP_METHOD_GET);
      }
      else {
        return $this->callGet($endpoint, $params);
      }
    }

    public function finalizeAuthorization($secret, $token, $verifier) {
      $oauth = new \OAuth($this->apiKey, $this->secret);
      $oauth->setToken($token, $secret);
      try {
        $response = $oauth->getAccessToken("https://openapi.etsy.com/v2/oauth/access_token", null, $verifier);
        $user = auth()->user();

        $user->oauthToken = $response["oauth_token"];
        $user->oauthTokenSecret = $response["oauth_token_secret"];
        $user->save();
        return true;
      }
      catch(\OAuthException $exception) {
        return false;
      }

    }

    public function getEtsyAuthorizeLink() {
      $a = $this->apiKey;
      $b = $this->secret;
      $oauth = new \OAuth($this->apiKey, $this->secret);
      $response = $oauth->getRequestToken("https://openapi.etsy.com/v2/oauth/request_token?scope=listings_w%20listings_r", route("completeAuthorization"));
//      dd($response);
      setcookie("token_secret", $response["oauth_token_secret"]);
      return $response["login_url"];
    }

    public function createShippingTemplate($request) {
      // create the shipping template that has both U.S> origin and destination.
      // Primary and secondary cost are equal (no shipping breaks for multiple items).
      $formData = [
        "title" => $request->title,
        "origin_country_id" => $request->origin_country_id,
        "primary_cost" => $request->ww_cost,
        "secondary_cost" => $request->ww_cost,
        ];
        // Etsy is returning an error when supplying these values. But should
        // include these.
        // "min_processing_days" => $request->min_processing_days,
        // "min_processing_days" => $request->max_processing_days,
        $response = $this->callOAuth("shipping/templates", $formData);
        $templateId = $response["results"][0]["shipping_template_id"];
        // Create a template entry for Canada
        $countryId = $this->fetchCountryIDByISO("CA");
        $data = ["destination_country_id" => $countryId, "cost" => $request->ca_cost];
        $this->createShippingTemplateEntry($templateId, $data);
        // Create a template entry for US
        $countryId = $this->fetchCountryIDByISO("US");
        $data = ["destination_country_id" => $countryId, "cost" => $request->us_cost];
        $this->createShippingTemplateEntry($templateId, $data);

        dd($response);
    }

    public function fetchCountryIDByISO($iso) {
      $response = $this->callGet("countries/iso/".$iso);
      return $response->results[0]->country_id;
    }

    public function createShippingTemplateEntry($templateId, $request) {
      // create the shipping template that has both U.S> origin and destination.
      // Primary and secondary cost are equal (no shipping breaks for multiple items).
      $formData = [
        "shipping_template_id" => $templateId,
        "destination_country_id" => $request["destination_country_id"],
        "primary_cost" => $request["cost"],
        "secondary_cost" => $request["cost"],
        ];
        $response = $this->callOAuth("shipping/templates/entries", $formData);
        return $response;
    }

    public function fetchSellerTaxonomy() {
      $response = $this->callGet("taxonomy/seller/get");
      return $response->results;
    }

    // listing ID 550543222
    // image URL https://gearbubble-assets.s3.amazonaws.com/5/1699738/43/235/front.png
    public function uploadImage($listingId, $imageUrl) {
        // Get the path to this app /temp/img.
        $path = dirname(realpath(__FILE__))."/temp/img";

        // If the /temp/img dir doesn't exist, create it.
        if(!is_dir($path)){
          mkdir($path, 0700, true);
        }

        // Get the image data from the remote file
        $imgData = file_get_contents($imageUrl);

        // The path to the temp image to create
        $imgPath = $path."/".$listingId;

        // Put the remote data in the temp file
        file_put_contents($imgPath, $imgData);

        // Provide the local path to the temp file so that oauth can upload it
        $formData = [
          "@image" => "@".$imgPath.";type=image/jpeg"
        ];

        $response = $this->callOAuth("/listings/".$listingId."/images", $formData, OAUTH_HTTP_METHOD_POST, true);

        // remote the file
        unlink($imgPath);

        // The response has a count property that is 1 if the file uploaded successfully. Otherwise, there will be an error.
        return $response["count"] == 1;
    }

    public function fetchShopCurrentUser() {
      // Use the __SELF__ token that Etsy supports to retrieve the shop for the current user.
      // This requires OAuth to work even though otherwise the endpoint does not require OAuth.
      $response = $this->callOAuth("users/__SELF__/shops", null, OAUTH_HTTP_METHOD_GET);
      return $response["results"][0];
    }

    public function createListing($request) {
      $formData = [
        "quantity" => "999",
        "title" => $request->title,
        "description" => $request->description,
        "price" => $request->price,
        "taxonomy_id" => $request->taxonomy_id,
        "tags" => $request->tags,
        "who_made" => "i_did",
        "when_made" => "made_to_order",
        "state" => "draft",
        "is_supply" => "false",
        "shipping_template_id" => $request->shippingTemplateId,
        "processing_min" => 7,
        "processing_max" => 14
        ];

      $response = $this->callOAuth("listings", $formData);

      $listing = $response["results"][0];
      $listingId = $listing["listing_id"];

      // Upload the two images for the mug. This will break if there are not two images.
      $this->uploadImage($listingId, $request->image1);
      $this->uploadImage($listingId, $request->image2);

      return $listing;
    }

    private function callGet($endpoint, $params="") {
      $client = new Client;
      $response = $client->request("GET", "https://openapi.etsy.com/v2/".$endpoint."?api_key=".$this->apiKey."".$params);
      return json_decode($response->getBody());
    }

    private function callOAuth($endpoint, $params, $method=OAUTH_HTTP_METHOD_POST, $requestEngineCurl = false) {
      $oauth = new OAuth($this->apiKey, $this->secret, OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_URI);
      $user = auth()->user();
      $oauth->setToken($user->oauthToken, $user->oauthTokenSecret);
      if($requestEngineCurl) {
        $oauth->setRequestEngine(OAUTH_REQENGINE_CURL);
      }
      $url = "https://openapi.etsy.com/v2/".$endpoint;
      try{
        //dd($params);
        if(count($params) == 0) {$params = null;}
        $response = $oauth->fetch($url, $params, $method);
        $json = $oauth->getLastResponse();
        $obj = json_decode($json, true);
        return $obj;
      }
      catch(\OAuthException $e) {
        dd($e);
      }
    }

}