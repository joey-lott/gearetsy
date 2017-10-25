<?php

namespace App\GearBubble\Utils;

use App\GearBubble\Models\Campaign;
use App\GearBubble\Models\CampaignColor;
use App\GearBubble\Models\PrimaryVariationTaxonomyGroup;
use App\Etsy\Models\TaxonomyPropertyCollection;
use App\Etsy\Models\ListingCollection;
use App\Etsy\Models\Listing;
use App\Etsy\Models\ListingProduct;
use App\Etsy\Models\ListingOffering;
use App\Etsy\Models\ListingStagingData;
use App\Etsy\Models\PropertyValue;
use App\GearBubble\Utils\ProductTypes;
use App\GearBubble\Models\ListingFormGroupCollection;
use App\GearBubble\Models\ListingFormGroup;
use App\Description;
use App\GearBubble\Models\PrimaryVariation;

class ListingsFormToListingCollection {


  private $listingCollection;
  private $request;

  public function __construct($request) {
    $this->request = $request;
  }

  public function validates() {
    $listingIds = explode(",", $this->request->listings);
    foreach($listingIds as $id) {
      $validator = validator()->make($this->request->all(), [
        "title_".$id => "required",
        "description_".$id => "required",
      ]);
      if($validator->fails()) return false;
    }
    return true;
  }

  public function getListingCollection() {
    if(isset($this->listingCollection)) return $this->listingCollection;
    $this->listingCollection = $listings = new ListingCollection();
    $listingIds = explode(",", $this->request->listings);
    foreach($listingIds as $id) {
      $listing = $this->constructListingForId($id, $this->request);
      $listings->add($listing);
    }
    return $this->listingCollection;
  }

  private function constructListingForId($id, $request) {

    $taxonomyId = $request["taxonomyId_".$id];
    $urls = $request["imageUrls_".$id];

    $primaryVariations = $this->constructPrimaryVariationsForId($id, $taxonomyId, $urls, $request);

    $title = $request["title_".$id];
    $description = $request["description_".$id];
    $tags = $request["tags_".$id];
    $price = $primaryVariations->getFirstPrice();
    $shippingTemplateId = $request["shippingTemplate_".$id];
    $listing = new Listing($title, $description, $price, $taxonomyId, $tags, $shippingTemplateId, $urls);

    $colors = $this->parseColorsForId($id, $request);
    $sizes = explode(",", $request["sizes_".$id]);
    $staging = $this->convertTaxonomyGroupToStaging($primaryVariations, $colors, $sizes, $request);
    $listing->staging = $staging;
    $listing->priceVariationPropertyId = isset($listing->staging->priceOnProperty) ? $listing->staging->priceOnProperty->property_id : "";

    return $listing;
  }

  private function parseColorsForId($id, $request) {
    $c = $request["colors_".$id];
    $colors = [];
    foreach($c as $cl) {
      array_push($colors, new CampaignColor($cl, $cl));
    }
    return $colors;
  }

  private function constructPrimaryVariationsForId($id, $taxonomyId, $urls, $request) {
    $pt = new ProductTypes();
    $pvtg = new PrimaryVariationTaxonomyGroup($taxonomyId);
    $pvtg->taxonomyId = $taxonomyId;
    $pvtg->addImageUrls($urls);
    $pvids = explode(",", $request["primaryVariationsCodes_".$id]);
    foreach($pvids as $pvid) {
      $description = $pt->getDisplayNameForProductId($pvid);
      $price = $request["primaryVariation_".$pvid];
      $pv = new PrimaryVariation($price, $description, $pvid);
      $pvtg->addPrimaryVariation($pv);
    }
    return $pvtg;
  }


  private function convertTaxonomyGroupToStaging($tg, $colors, $sizes, $request) {

    $s = new ListingStagingData();

    $pt = new ProductTypes();

    $taxonomyId = $tg->taxonomyId;

    $tpc = TaxonomyPropertyCollection::createFromTaxonomyId($taxonomyId);

    // Iterate through all the primary variations (i.e. the different product types
    // within a taxonomy group). In other words, unisex short sleeve tee shirts and long sleeve tee shirts
    // are in the same taxonomy. But there are two primary variation - short sleeve and
    // long sleeve. Each has a different offering because each has a different price.
    // There should always be at least one primary variation.
    foreach($tg->primaryVariations as $pv) {
      // First, create the offering based on the price.
      $offering = new ListingOffering($pv->price);

      // Next, determine which property values need to be included
      // in the product. Property values in this context can include
      // the primary variation (if there is more than one), size (if there is
      // more than one), and color (if there is more than one).
      $variationOnPrimaryVariation = count($tg->primaryVariations) > 1;
      $variationOnColor = count($colors) > 1;
      $variationOnSize = count($sizes) > 1;

      // These are the property values to apply to the listing product
      $propertyValues1 = [];

      $primaryColorProperty = $tpc->propertyByName("Primary color");
      $sizeProperty = $tpc->propertyByName("Size");

      // If there is more than one primary variation, add a property value for the
      // primary variation. Otherwise, don't. The property value is used by Etsy to
      // display variations. If there is only one primary variation, we don't want
      // Etsy to display an option select box with just one value.
      if($variationOnPrimaryVariation) {

        // The property name is the something like "Volume" or "Size". These are specific
        // to Etsy. I need a way to map GB products to Etsy variation types (eg. mugs
        // will have a Volume variation for Etsy). So I'm mapping the variation property name
        // to the product codes in ProductTypes.
        $variationPropertyName = $pt->getVariationPropertyForProductId($pv->productCode);

        // The variation property is a TaxonomyProperty object. I need this to get the
        // property ID to pass to the PropertyValue object.
        $variationProperty = $tpc->propertyByName($variationPropertyName);

        // The value is specific to a product code from GB. For example, GB code
        // 20 is an 11 oz mug. The value is 11. That is because Etsy will need a numeric
        // value. So I'm keeping the values in the ProductTypes map as well.
        $val = $pt->getValueForProductId($pv->productCode);

        // As with property, I also map Etsy-specific scales to GB product types. A scale
        // is something like "Fluid ounces" or "Milliliters". Scales may have null properties
        // in some cases. For example, when the variation property name is "Style", as in the
        // case of shirts, there is no scale. This is handled gracefully by the rest of the code.
        $scaleName = $pt->getScaleForProductId($pv->productCode);

        // The scale is a TaxonomyPropertyScale object. I need this to get the
        // scale ID to pass to the PropertyValue object.
        $scale = $variationProperty->getScaleByName($scaleName);

        // The property value contains the Etsy property ID, the Etsy scale ID, and the value (eg. the number of fluid ounces for a mug)
        $pv = new PropertyValue($variationProperty->property_id, $scale->scale_id, [$val]);

        $s->priceOnProperty = $pv;

        array_push($propertyValues1, $pv);
      }

      // Iterate over each of the colors as well. As with primary variations, if there
      // is only one color, we'll avoid adding a property value.
      foreach($colors as $color) {

        $propertyValues2 = array_slice($propertyValues1, 0);

        // Only if there are multiple colors should we add a property value for the color.
        if($variationOnColor) {
            // Add property value for the color.
            $colorPv = new PropertyValue($primaryColorProperty->property_id, null, [$pt->getColorNameById($color->id)]);
            array_push($propertyValues2, $colorPv);
        }

        if(count($sizes) > 1) {
          foreach($sizes as $size) {
            $pval = $sizeProperty->getPossibleValueByName($size);
//            echo "size: ".$size."\n";

            if(isset($pval)) {
              $sizePv = new PropertyValue($sizeProperty->property_id, $pval->scale_id, null, [$pval->value_id]);

              // Clone the property values array so as not to change the existing one.
              $propertyValues3 = array_slice($propertyValues2, 0);
              array_push($propertyValues3, $sizePv);

              $lp = new ListingProduct($propertyValues3, [$offering]);
              $s->addProduct($lp);
            }
          }
        }
        else {
          $lp = new ListingProduct($propertyValues2, [$offering]);
          $s->addProduct($lp);
        }
      }
    }
    //dump($s);
    return $s;
  }

  // Map the GB sizes (sml, med, lrg) to Etsy sizes (S, M, L)
  private function mapGBSizesToEtsy($sizes) {
    $etsy = [];
    $pt = new ProductTypes();
    foreach($sizes as $size) {
      if(isset($size)) {
        $etsySize = $pt->mapSize($size);
        if(isset($etsySize)) {
          array_push($etsy, $etsySize);
        }
      }
    }
    return $etsy;
  }

  public function getFormFieldCollection() {
    $c = $this->getListingCollection();
    $ffc = new ListingFormGroupCollection();
    for($i = 0; $i < $c->count(); $i++) {
      $listing = $c->getAt($i);
      $lfg = new ListingFormGroup($listing, $this->descriptions, $this->shippingTemplates);
      $ffc->add($lfg);
    }
    return $ffc;
  }

}
