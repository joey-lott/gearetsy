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

}
