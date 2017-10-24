<?php

namespace App\GearBubble\Models;

class ListingFormGroupCollection {

  private $collection = [];

  public function add($lfg) {
    array_push($this->collection, $lfg);
  }

  public function count() {
    return count($this->collection);
  }

  public function getAt($i) {
    return $this->collection[$i];
  }

  public function getListingStagingIdString() {
    $ids = [];
    foreach($this->collection as $lfg) {
      array_push($ids, $lfg->stagingId);
    }
    return implode(",", $ids);
  }

}
