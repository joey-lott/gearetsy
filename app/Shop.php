<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model {

  protected $fillable = ["name"];

  // Create a new Shop from the data returned for a singular shop
  // from the Etsy Restful API call to shop/id
  public static function createFromAPIResponse($userId, $apiResponse) {
    $shop = new Shop;
    // To start, only set the name. May need to add more
    // properties later.
    $shop->name = $apiResponse->shop_name;
    $shop->shop_id = $apiResponse->shop_id;
    $shop->user_id = $userId;
    return $shop;
  }

  public function listings() {
    $api = resolve("\App\EtsyAPI");
    $listings = $api->fetchListings($this->name, \App\EtsyAPI::$PAGE_LISTINGS);
    return $listings;
  }

}
