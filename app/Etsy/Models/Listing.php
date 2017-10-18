<?php

namespace App\Etsy\Models;

class Listing {

  public $quantity = 999;
  public $title;
  public $description;
  public $price;
  public $taxonomy_id;
  public $tags;
  public $who_made = "i_did";
  public $when_made = "made_to_order";
  public $state = "draft";
  public $is_supply = "false";
  public $shipping_template_id;
  public $processing_min = 7;
  public $processing_max = 14;
  public $imagesToAddFromUrl = [];

  public function __construct($t, $d, $p, $tid, $tags, $stid) {
    $this->title = $t;
    $this->description = $d;
    $this->price = $p;
    $this->taxonomy_id = $tid;
    $this->tags = $tags;
    $this->shipping_template_id = $stid;
  }

  public function addImageUrl($url) {
    array_push($this->imagesToAddFromUrl, $url);
  }


}
