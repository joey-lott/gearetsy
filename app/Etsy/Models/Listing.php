<?php

namespace App\Etsy\Models;

use App\Etsy\Models\ListingStagingData;
use App\Etsy\Models\ListingInventory;

class Listing {

  public $quantity = 999;
  public $title;
  public $description;
  public $price;
  public $taxonomy_id;
  public $tags;
  public $who_made = "i_did";
  public $when_made = "made_to_order";
  public $state = "draft";
  public $is_supply = "false";
  public $shipping_template_id;
  public $processing_min = 7;
  public $processing_max = 14;
  public $imagesToAddFromUrl = [];

  public $inventory;
  public $staging;
  public $priceVariationPropertyId;

  public function __construct($t, $d, $p, $tid, $tags, $stid, $urls) {
    $this->title = $t;
    $this->description = $d;
    $this->price = $p;
    $this->taxonomy_id = $tid;
    $this->tags = $tags;
    $this->shipping_template_id = $stid;
    $this->imagesToAddFromUrl = $urls;
    $this->staging = new ListingStagingData();
  }

  public function addImageUrl($url) {
    array_push($this->imagesToAddFromUrl, $url);
  }

  public function saveToEtsy() {
    if(!isset($this->price)) {
      $this->price = $this->staging->getLowestPrice();
    }
    $api = resolve("\App\Etsy\EtsyAPI");
    $listing = $api->createListing($this);

    if($this->staging->hasProducts()) {
      $this->inventory = new ListingInventory($listing["listing_id"], $this->staging->products, $this->priceVariationPropertyId);
      $this->inventory->saveToEtsy();
    }

    return $listing;
  }

  public function imagesToAddFromUrlReversed() {
    return array_reverse($this->imagesToAddFromUrl);
  }

}
