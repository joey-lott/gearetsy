<?php

namespace App\GearBubble\Utils;

use GuzzleHttp\Client as GClient;
use Goutte\Client;
use App\Description;

class PageScraper {

  private $url;
  private $results;
  private $products = ["20" => "11 oz",
                       "43" => "15 oz",
                       "22" => "Unisex Tee",
                       "28" => "Women's Tee",
                       "35" => "Youth Tee",
                       "36" => "Youth Hoodie",
                       "41" => "Sweatshirt",
                       "56" => "Zip Hoodie",
                       "57" => "Long Sleeve Tee",
                        "29" => "Hoodie",
                        "31" => "Necklace"];

  public function __construct($url) {
      $this->url = $url;
  }

  public function getResults() {
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

        // "order-style-id" is a class that identifies the primary variation type. If there is more than
        // one variation, there will be a select with child options. Otherwise, there is just one variation
        // and this will be a hidden input.
        $orderStyles = $crawler->filterXPath('//select[contains(@class, "order-style-id")]');
        if($orderStyles->count() > 0) {
          foreach($orderStyles->children() as $orderStyle) {
            $stylePrice = $orderStyle->getAttribute('data-cost');
            $styleCode = $orderStyle->getAttribute('value');
            array_push($primaryVariations, ["price" => $stylePrice, "desc" => $this->products[$styleCode], "productCode" => $styleCode]);
          }
        }
        else {
          $price = $crawler->filterXPath('//input[contains(@id, "price_")]')->extract("value")[0];//->evaluate('substring-after(_text, "$ ")')[0];
          $type = $crawler->filterXPath('//input[contains(@class, "order-style-id")]')->extract('value')[0];
          array_push($primaryVariations, ["price" => $price, "desc" => $this->products[$type], "productCode" => $type]);
//          dd($orderStyle);
        }

        $variations = [];

        // If there are variations, the additional_style_id select element will appear on the page. In that case,
        // extract the variations.
        /*if($crawler->evaluate('count(//select[contains(@id, "additional_style_id")])')[0] > 0) {
          $variationsCrawler = $crawler->filterXPath('//select[contains(@id, "additional_style_id")]')->children();
          foreach($variationsCrawler as $variation) {
            $variationPrice = $variation->getAttribute("data-cost");
            $variationType = $variation->getAttribute("value");
            $variations[$variationType] = ["price" => $variationPrice, "desc" => $this->products[$variationType], "productCode" => $variationType];
          }
        }*/


        $colors = [];
        $colorsWrapperNode = $crawler->filterXPath("//div[contains(@class, 'colors-wrapper')]")->first();
        if($colorsWrapperNode->count()) {
          $colors = $colorsWrapperNode->children()->extract("data-id");
        }
        else {
          $singleColorValue = $crawler->filterXPath("//input[contains(@id, 'color_')]")->extract("value")[0];
          array_push($colors, $singleColorValue);
        }

        //dd($colors);
// Image URL format: https://gearbubble-assets.s3.amazonaws.com/productId/campaignId/styleCode/colorId/front.png
        //dd($campaignId);
        $imageUrls = [];
        foreach($colors as $color) {
          foreach($primaryVariations as $styleVariation) {
            $url = "https://gearbubble-assets.s3.amazonaws.com/".$productId."/".$campaignId."/".$styleVariation["productCode"]."/".$color."/front.png";
            array_push($imageUrls, $url);
          }
        }


        $descriptions = Description::where("user_id", auth()->user()->id)->get()->all();
        $this->results = ["imageUrls" => $imageUrls,
                          "title" => $title,
                          "url" => $url,
                          "descriptions" => $descriptions,
                          "primaryVariations" => $primaryVariations];
        return true;
      }
      return false;

    }
  }



}
