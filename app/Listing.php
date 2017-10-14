<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Listing extends Model {

  public static function createFromAPIResponse($apiResponse) {
    $listing = new Listing;
    $listing->name = $apiResponse->title;
    $listing->etsyId = $apiResponse->id;
    return $shop;
  }
}
