<?php

namespace App\Etsy\Models;

use App\Etsy\Models;

class TaxonomyProperty {

  public $property_id;
  public $name;
  public $display_name;
  public $is_required;
  public $supports_attributes;
  public $supports_variations;
  public $is_multivalued;
  public $scales = [];
  public $possible_values = [];
  public $selected_values = [];

  static public function createFromAPIResponse($response) {
    $tp = new TaxonomyProperty;
    $tp->property_id = $response["property_id"];
    $tp->name = $response["name"];
    $tp->display_name = $response["display_name"];
    $tp->is_required = $response["is_required"];
    $tp->supports_attributes = $response["supports_attributes"];
    $tp->supports_variations = $response["supports_variations"];
    $tp->is_multivalued = $response["is_multivalued"];
    foreach($response["scales"] as $scale) {
      $tps = TaxonomyPropertyScale::createFromAPIResponse($scale);
      array_push($tp->scales, $tps);
    }
    $tp->possible_values = $response["possible_values"];
    $tp->selected_values = $response["selected_values"];
    return $tp;
  }

  public function getScaleByName($name) {
    foreach($this->scales as $scale) {
      if($scale->display_name == $name) {
        return $scale;
      }
    }
  }

}
