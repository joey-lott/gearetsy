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

  private $campaignToListingCollection;

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
    $this->campaignToListingCollection = new CampaignToListingCollection($this);
    return $this->campaignToListingCollection->getListingCollection();
  }

  public function mustBeSplitIntoOnePrimaryVariationPerListing() {
    // if has multiple primary variations && multiple colors && multiple sizes, return true
    return (count($this->primaryVariations) > 1 &&
       count($this->colors) > 1 &&
       count($this->sizes) > 1);
  }

  public function getFormFieldCollection() {
    if(!isset($this->campaignToListingCollection)) $this->getListingCollection();
    return $this->campaignToListingCollection->getFormFieldCollection();
  }

}
