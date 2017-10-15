<?php

namespace App\Etsy\Models;

use App\Etsy\Models;

class PropertyValue {

  public $property_id;
  public $property_name;
  public $scale_id;
  public $scale_name;
  public $value_ids = [];
  public $values = [];

  public function __construct($id, $sid, $vs) {
    $this->property_id = $id;
    $this->scale_id = $sid;
    $this->values = $vs;
  }

}
