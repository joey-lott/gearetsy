<?php

namespace App\Etsy\Models;

use App\Etsy\Models\EtsyModel;

class ListingProduct extends EtsyModel {

  public $product_id;
  public $property_values;
  public $sku = "";
  public $offerings = [];
  public $is_deleted;

  public function __construct($p = [], $o = [], $s = "") {
    $this->property_values = $p;
    $this->sku = $s;
    $this->offerings = $o;
  }

  public function jsonSerialize() {
    return ["property_values" => $this->property_values,
            "sku" => $this->sku,
            "offerings" => $this->offerings];
  }

}
