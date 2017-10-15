<?php

use App\Etsy\Models;

class ListingOffering {

  public $offering_id;
  public $price;
  public $quantity;
  public $is_enabled;
  public $is_deleted;

  public function __construct($p. $q = 999, $e = 1) {
    $this-price = $p;
    $this->quantity = $q;
    $this->is_enabled = $e;
  }

}
