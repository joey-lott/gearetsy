<?php

use App\Etsy\Models;

class ListingInventory {

  public $listing_id;
  public $products = [];

  public function __construct($id, $products) {
    $this->listing_id = $id;
    $this->products = $products;
  }

  public function addProduct($product) {
    array_push($this->products, $product);
  }

}
