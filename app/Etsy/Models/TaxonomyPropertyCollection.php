<?php

namespace App\Etsy\Models;


use App\Etsy\Models\TaxonomyProperty;
use Illuminate\Support\Facades\Storage;

class TaxonomyPropertyCollection {

  public $properties = [];

  static public function createFromAPIResponse($response) {
    $tpc = new TaxonomyPropertyCollection;
    foreach($response as $t) {
      array_push($tpc->properties, TaxonomyProperty::createFromAPIResponse($t));
    }
    return $tpc;
  }

  static public function createFromTaxonomyId($id) {
    // Because this data doesn't change often, and because
    // Etsy API calls are "expensive" (limited per day),
    // store this to disk locally and read it from there whenever
    // possible.
    if(Storage::exists('taxonomy_properties_'.$id.'.json')) {
      $tprops = json_decode(Storage::get('taxonomy_properties_'.$id.'.json'));
    }
    else {
      $api = resolve("\App\Etsy\EtsyAPI");
      $tprops = $api->fetchTaxonomyProperties($id);
      Storage::put('taxonomy_properties_'.$id.'.json', json_encode($tprops));
    }
    return TaxonomyPropertyCollection::createFromAPIResponse($tprops);
  }

  public function addTaxonomyProperty($tp) {
    array_push($this->properties, $tp);
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
      if($property->name == $name || $property->display_name == $name) {
        return $property;
      }
    }

  }

}
