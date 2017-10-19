<?php
namespace App\GearBubble\Models;

class PrimaryVariation {

  public $price;
  public $description;
  public $productCode;

  public function __construct($p, $d, $pc) {
    $this->price = $p;
    $this->description = $d;
    $this->productCode = $pc;
  }

}
