<?php
namespace App\GearBubble\Models;

class PrimaryVariationTaxonomyGroup {

  public $taxonomyId;
  public $imageUrls = [];
  public $primaryVariations = [];

  public function __construct($tid) {
    $this->taxonomyId = $tid;
  }

  public function addPrimaryVariation($pv) {
    array_push($this->primaryVariations, $pv);
  }

  public function addImageUrls($urls) {
    if(!(isset($urls))) return;
    foreach($urls as $url) {
      array_push($this->imageUrls, $url);
    }
  }

  public function getTaxonomyDisplay() {
    $taxonomyLabel = "";
    foreach($this->primaryVariations as $pv) {
      $taxonomyLabel = $taxonomyLabel.$pv->description.",";
    }
    return $taxonomyLabel;
  }

  public function getFirstPrice() {
    $pv = $this->primaryVariations[0];
    return isset($pv) ? $pv->price : 0;
  }

}
