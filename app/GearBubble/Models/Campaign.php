<?php
namespace App\GearBubble\Models;

use App\Etsy\Models\ListingCollection;
use App\GearBubble\Utils\CampaignToListingCollection;

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

  // Convert the GB campaign to Etsy listings and put in a collection
  public function getListingCollection() {
    $ctlc = new CampaignToListingCollection($this);
    return $ctlc->getListingCollection();
  }

}
