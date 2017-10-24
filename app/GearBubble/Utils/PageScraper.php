<?php

namespace App\GearBubble\Utils;

use GuzzleHttp\Client as GClient;
use Goutte\Client;
use App\Description;
use App\GearBubble\Models\PrimaryVariation;
use App\GearBubble\Models\Campaign;
use App\GearBubble\Models\CampaignColor;

class PageScraper {

  public $url;
  private $results;

  public function __construct($url) {
      $this->url = $url;
  }

  public function getCampaign() {
    return $this->results;
  }

  public function scrape() {

    $url = $this->url;

    $client = new Client;

    $crawler = $client->request('GET', $url);

    // Check that there is a meta tag with a site name of Gearbubble. This appears on GearBubble pages.
    // If it doesn't appear, display an error message.
    $siteName = $crawler->filterXPath('//meta[contains(@property, "og:site_name")]')->extract("content");

    $isGearBubble = false;
    if(count($siteName) > 0) {
      $isGearBubble = $siteName[0] == "Gearbubble";
    }

    // If it is GearBubble, verify that it contains a campaign ID. If so, scrape the whole
    // page.
    if($isGearBubble) {
//      $imageUrls = $crawler->filter('a.thumb')->extract("data-product-image");
      $campaignIdNode = $crawler->filterXPath('//input[contains(@id, "order_campaign_id")]')->extract("value");

      if(count($campaignIdNode)) {

        $campaignId = $campaignIdNode[0];

        // The product ID is needed to grab images. But it is only available on the page in the <script> tag text.
        $pageOptions = $crawler->filterXPath('//script[contains(text(), "pageOptions")]')->evaluate('substring-after(text(), "productId\':")')[0];
        $productId = intval(explode(",", $pageOptions)[0]);

        $title = $crawler->filter('h4.form-title')->extract("_text")[0];

        //$type = $crawler->filterXPath('//a[contains(@data-target, "#sizing-chart-for-style-")]')->evaluate('substring-after(@data-target, "#sizing-chart-for-style-")')[0];

        // The primary variation is the variation on which the price changes. Typically this is size. For
        // example, mug prices vary on size/volume (11 oz v 15 oz) and shirt prices vary on style (unisex tee, women's tee, long sleep tee, etc).
        // Shirts are complex because they can also vary on size. But because Etsy only allows one price variation.
        // I'll force it to vary on style for now...that is easiest and most common use case.
        $primaryVariations = [];

        $pt = new ProductTypes();

        // "order-style-id" is a class that identifies the primary variation type. If there is more than
        // one variation, there will be a select with child options. Otherwise, there is just one variation
        // and this will be a hidden input.
        $orderStyles = $crawler->filterXPath('//select[contains(@class, "order-style-id")]');
        if($orderStyles->count() > 0) {
          foreach($orderStyles->children() as $orderStyle) {
            $stylePrice = $orderStyle->getAttribute('data-cost');
            $styleCode = $orderStyle->getAttribute('value');
            $primaryVariation = new PrimaryVariation($stylePrice, $pt->getDisplayNameForProductId($styleCode), $styleCode);
            array_push($primaryVariations, $primaryVariation);
          }
        }
        else {
          $price = $crawler->filterXPath('//input[contains(@id, "price_")]')->extract("value")[0];//->evaluate('substring-after(_text, "$ ")')[0];
          $type = $crawler->filterXPath('//input[contains(@class, "order-style-id")]')->extract('value')[0];
          $primaryVariation = new PrimaryVariation($price, $pt->getDisplayNameForProductId($type), $type);
          array_push($primaryVariations, $primaryVariation);
        }

        $sizes = [];
        $sizeOptions = $crawler->filterXPath("//select[contains(@id, 'size_')]/option[string-length(@value) > 0]");
        if($sizeOptions->count()) {
          $sizes = $sizeOptions->extract("value");
        }

        $colors = [];
        $colorsWrapperNode = $crawler->filterXPath("//div[contains(@class, 'colors-wrapper')]")->first();
        if($colorsWrapperNode->count()) {
          $tmpColors = $colorsWrapperNode->children()->extract(["data-id", "data-color"]);
          foreach($tmpColors as $c) {
            array_push($colors, new CampaignColor($c[0], $c[1]));
          }
        }
        else {
          $singleColorValue = $crawler->filterXPath("//input[contains(@id, 'color_')]")->extract("value")[0];
          array_push($colors, new CampaignColor($singleColorValue, ""));
        }

        //dd($colors);
// Image URL format: https://gearbubble-assets.s3.amazonaws.com/productId/campaignId/styleCode/colorId/front.png
        //dd($campaignId);
        $imageUrls = [];
        $imageUrlsByProductCode = [];
        foreach($colors as $color) {
          $colorId = $color->id;
          foreach($primaryVariations as $styleVariation) {
            $imgUrl = "https://gearbubble-assets.s3.amazonaws.com/".$productId."/".$campaignId."/".$styleVariation->productCode."/".$colorId."/front.png";
            array_push($imageUrls, $imgUrl);
            if(!isset($imageUrlsByProductCode[$styleVariation->productCode])) {
              $imageUrlsByProductCode[$styleVariation->productCode] = [];
            }
            array_push($imageUrlsByProductCode[$styleVariation->productCode], $imgUrl);

            // Do the same for the back image if the product type has multiple imageUrls
            if($pt->hasMultipleImages($styleVariation->productCode)) {
              $imgUrl = "https://gearbubble-assets.s3.amazonaws.com/".$productId."/".$campaignId."/".$styleVariation->productCode."/".$colorId."/back.png";
              array_push($imageUrls, $imgUrl);
              array_push($imageUrlsByProductCode[$styleVariation->productCode], $imgUrl);
            }
          }
        }


        $this->results = new Campaign($title, $campaignId, $url, $primaryVariations, $colors, $imageUrls, $imageUrlsByProductCode, $sizes);
        return true;
      }
      return false;

    }
  }




}
