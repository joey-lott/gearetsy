<?php
namespace App\GearBubble\Models;

class Campaign {

  public $title;
  public $campaignId;
  public $url;
  public $primaryVariations = [];
  public $colors = [];
  public $imageUrls;
  public $imageUrlsByProductCode = [];
  public $sizes = [];

  public function __construct($t, $cid, $url, $pvs, $colors, $imageUrls, $iubpc, $sizes) {
    $this->title = $t;
    $this->cid = $cid;
    $this->url = $url;
    $this->primaryVariations = $pvs;
    $this->colors = $colors;
    $this->imageUrls = $imageUrls;
    $this->imageUrlsByProductCode = $iubpc;
    $this->sizes = $sizes;
  }

}
