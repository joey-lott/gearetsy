<?php

namespace App\GearBubble\Utils;

use GuzzleHttp\Client as GClient;
use Goutte\Client;
use App\Description;

class PageScraper {

  private $url;
  private $results;
  private $products = ["20" => "11 oz",
                       "43" => "15 oz"];

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

        $variations = [];

        // If there are variations, the additional_style_id select element will appear on the page. In that case,
        // extract the variations.
        if($crawler->evaluate('count(//select[contains(@id, "additional_style_id")])')[0] > 0) {
          $variationsCrawler = $crawler->filterXPath('//select[contains(@id, "additional_style_id")]')->children();
          foreach($variationsCrawler as $variation) {
            $variationPrice = $variation->getAttribute("data-cost");
            $variationType = $variation->getAttribute("value");
            $variations[$variationType] = ["price" => $variationPrice, "desc" => $this->products[$variationType], "productCode" => $variationType];
          }
        }

        $descriptions = Description::where("user_id", auth()->user()->id)->get()->all();
        $this->results = ["imageUrls" => $imageUrls,
                          "title" => $title,
                          "type" => $type,
                          "price" => $extractedPrice,
                          "url" => $url,
                          "descriptions" => $descriptions,
                          "variations" => $variations];
        return true;
      }
      return false;

    }
  }



}
