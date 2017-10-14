<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client as GClient;
use Goutte\Client;
use App\Shop;
use App\Description;

class ListingController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create() {
      return view("shop.gburlform");
    }

    // Once the user has input a GB URL, scrape the GB campaign page and display
    // the results to the user. Prompt the user to confirm that this is the correct
    // campaign information, and require that he fill out a few other fields
    // such as tags, description, etc.
    public function confirm() {

      if(!isset(request()->url)) {
        $url = session('url');
      }
      else {
        $url = request()->url;
      }

      // Scrape the GB campaign page. Currently not doing anything to handle if the user inputs
      // an invalid URL.
      $client = new Client;

      $crawler = $client->request('GET', $url);

      // Check that there is a meta tag with a site name of Gearbubble. This appears on GearBubble pages.
      // If it doesn't appear, display an error message.
      $siteName = $crawler->filterXPath('//meta[contains(@property, "og:site_name")]')->extract("content");

      $isGearBubble = false;
      if(count($siteName) > 0) {
        $isGearBubble = $siteName[0] == "Gearbubble";
      }

      // If it is GearBubble, verify that it contains product images. If so, scrape the whole
      // page.
      if($isGearBubble) {
        $imageUrls = $crawler->filter('a.thumb')->extract("data-product-image");
        if(count($imageUrls) > 0) {
          $title = $crawler->filter('h4.form-title')->extract("_text")[0];
          $price = $crawler->filterXPath('//span[contains(@class, "inner-circle")]')->extract("_text")[0];//->evaluate('substring-after(_text, "$ ")')[0];
          $regex = "/[1-9]\d*\.\d+/";
          preg_match($regex, $price, $priceMatch);
          $extractedPrice = $priceMatch[0];
          $type = $crawler->filterXPath('//a[contains(@data-target, "#sizing-chart-for-style-")]')->evaluate('substring-after(@data-target, "#sizing-chart-for-style-")')[0];
          $api = resolve("\App\EtsyAPI");
          $taxonomy = $api->fetchSellerTaxonomy();

          // Get the taxonomy for Mugs. Passing this single value to the view for now.
          // Basically, hard coding for mugs only. Will support multiple taxonomies in the future.
          $mugId = $this->findMugTaxonomyId($taxonomy);

          $shippingTemplates = $api->fetchShippingTemplates(auth()->user()->etsyUserId);

          $descriptions = Description::where("user_id", auth()->user()->id)->get()->all();

          return view("shop.listingconfirm", ["imageUrls" => $imageUrls,
                                              "title" => $title,
                                              "type" => $type,
                                              "price" => $extractedPrice,
                                              "shippingTemplates" => $shippingTemplates,
                                              "taxonomy" => $mugId,
                                              "url" => $url,
                                              "descriptions" => $descriptions]);
        }
        else {
          $error = "The URL you provided appears to be a GearBubble page, but it does not appear to be a valid campaign.";
        }
      }
      else {
        $error = "The URL you provided is not a GearBubble page. This app only works with GearBubble campaigns.";
      }
      return redirect()->back()->withErrors(["error" => $error]);
    }

    private function findMugTaxonomyId($taxonomy) {
      foreach($taxonomy as $item) {
        if($item->name == "Mugs") {
          return $item->id;
        }
        if(isset($item->children)) {
          $mugId = $this->findMugTaxonomyId($item->children);
          if(isset($mugId)) {
            return $mugId;
          }
        }
      }
    }

    public function submit(Request $request) {
      $validator = validator()->make($request->all(), [
        "title" => "required",
        "price" => "required|numeric",
        "description" => "required"
      ]);
      if($validator->fails()) {
        return redirect()->back()->withErrors($validator)->with(["url" => $request->url]);
      }
      $api = resolve("\App\EtsyAPI");
      $listing = $api->createListing($request);

      // Redirect to the starting point for listing. This does two things:
      // 1. It prevents a refresh from resubmitting and creating a duplicate listing
      // 2. It cycles the user back to list another product. This is the most common use case
      return redirect("/listing/create")->with(["listing" => $listing]);
    }

}
