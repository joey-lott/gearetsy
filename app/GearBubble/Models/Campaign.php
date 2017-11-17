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
    $this->title = $this->formatTitle($t);
    $this->cid = $cid;
    $this->url = $url;
    $this->primaryVariations = $pvs;
    $this->colors = $colors;
    $this->imageUrls = $imageUrls;
    $this->imageUrlsByProductCode = $iubpc;
    $this->sizes = $sizes;
  }

  public function formatTitle($title) {

    // Make sure the title starts with a letter or number
    $title = preg_replace('/^[^a-zA-Z0-9]+(?=[a-zA-Z0-9])/', "", $title);

    // Remove $ and ^ from the title because they are not allowed
    $title = preg_replace('/[\$\^]/', "", $title);

    // Etsy only allows one &, so remove all but the first (if there are any)
    $titleChunks = explode("&", $title);
    if(count($titleChunks) > 1) {
       $titleChunks[0] .= "&";
    }
    $title = implode("", $titleChunks);

    // Etsy only allows one :, so remove all but the first (if there are any)
    $titleChunks = explode(":", $title);
    if(count($titleChunks) > 1) {
       $titleChunks[0] .= ":";
    }
    $title = implode("", $titleChunks);

    // Etsy only allows one %, so remove all but the first (if there are any)
    $titleChunks = explode("%", $title);
    if(count($titleChunks) > 1) {
       $titleChunks[0] .= "%";
    }
    $title = implode("", $titleChunks);

    if(strlen($title) > 140) {
      $title = substr($title, 0, 140);
      $words = explode(" ", $title);
      array_pop($words);
      $title = implode(" ", $words);
    }
    return $title;
  }

  // Convert the GB campaign to Etsy listings and put in a collection
  public function getListingCollection($forceOnePrimaryVariationPerListing = false) {
    $this->campaignToListingCollection = new CampaignToListingCollection($this);
    return $this->campaignToListingCollection->getListingCollection($forceOnePrimaryVariationPerListing);
  }

  public function mustBeSplitIntoOnePrimaryVariationPerListing() {
    // if has multiple primary variations && multiple colors && multiple sizes, return true
    return (count($this->primaryVariations) > 1 &&
       count($this->colors) > 1 &&
       count($this->sizes) > 1);
  }

  public function getFormFieldCollection($forceOnePrimaryVariationPerListing = false) {
    if(!isset($this->campaignToListingCollection)) $this->getListingCollection($forceOnePrimaryVariationPerListing);
    return $this->campaignToListingCollection->getFormFieldCollection($forceOnePrimaryVariationPerListing);
  }

  public function downloadImagesAndMakeThumbnails() {

  }

}
