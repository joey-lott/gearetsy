<?php

namespace App\Etsy\Models;

use App\Etsy\Models\EtsyModel;

class PropertyValue extends EtsyModel {

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

  public function jsonSerialize() {
    return ["property_id" => $this->property_id,
            "scale_id" => $this->scale_id,
            "values" => $this->values];
  }

}
