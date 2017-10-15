<?php

namespace App\Etsy\Models;


use App\Etsy\Models\TaxonomyProperty;

class TaxonomyPropertyCollection {

  public $properties = [];

  static public function createFromAPIResponse($response) {
    $tpc = new TaxonomyPropertyCollection;
    foreach($response as $t) {
      array_push($tpc->properties, TaxonomyProperty::createFromAPIResponse($t));
    }
    return $tpc;
  }

  public function filter($enum) {
    $tpc = new TaxonomyPropertyCollection;
    switch ($enum) {
      case 'supports_variations':
        foreach($this->properties as $property) {
          if($property->supports_variations) {
            array_push($tpc->properties, $property);
          }
        }
        break;
    }
    return $tpc;
  }

  public function all() {
    return $this->properties;
  }

  public function propertyByName($name) {
    foreach($this->properties as $property) {
      if($property->name == $name) {
        return $property;
      }
    }

  }

}
