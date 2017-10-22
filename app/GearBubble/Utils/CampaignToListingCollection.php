<?php

namespace App\GearBubble\Utils;

use App\GearBubble\Models\Campaign;
use App\GearBubble\Models\PrimaryVariationTaxonomyGroup;
use App\Etsy\Models\ListingCollection;
use App\Etsy\Models\Listing;

class CampaignToListingCollection {

  private $campaign;

  public function __construct($campaign) {
      $this->campaign = $campaign;
  }

  public function getListingCollection() {

    $tgs = $this->groupByTaxonomies();
    $lc = $this->convertTaxonomyGroupsToListingCollection($tgs);
    return $lc;
  }

  // Group the variations by taxonomy. For example, 11 oz and 15 oz mugs have the same taxonomy.
  // But different shirts have different taxonomies (eg. hoodies and t-shirts)
  private function groupByTaxonomies() {
    $pt = new ProductTypes();

    $tids = [];
    foreach($this->campaign->primaryVariations as $primaryVariation) {
      $productCode = $primaryVariation->productCode;
      $taxonomyId = $pt->getTaxonomyIdForProductId($productCode);
      if(!isset($tids[$taxonomyId])) {
        $tids[$taxonomyId] = new PrimaryVariationTaxonomyGroup($taxonomyId);
      }
      $tids[$taxonomyId]->addPrimaryVariation($primaryVariation);
      $tids[$taxonomyId]->addImageUrls($this->campaign->imageUrlsByProductCode[$productCode]);
    }
    return $tids;
  }

  // Convert the primary variation taxonomy groups into listings and add to a collection.
  private function convertTaxonomyGroupsToListingCollection($tgs) {
    $listings = new ListingCollection();
    foreach($tgs as $tg) {
      $listing = new Listing($this->campaign->title, "", $tg->getFirstPrice(), $tg->taxonomyId, "", null, $tg->imageUrls);
      $listings->add($listing);
    }
    return $listings;
  }


}
