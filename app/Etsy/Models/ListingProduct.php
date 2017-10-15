<?php

use App\Etsy\Models;

class ListingProduct {

  public $product_id;
  public $property_values;
  public $sku = "";
  public $offerings = [];
  public $is_deleted;

  public function __construct($p = [], $s = "", $o = []) {
    $this-property_values = $p;
    $this->sku = $s;
    $this->$offerings = $e;
  }

}
