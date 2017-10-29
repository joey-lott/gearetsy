<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client as GClient;
use Goutte\Client;
use App\Shop;
use App\Description;
use App\GearBubble\Utils\PageScraper;
use App\GearBubble\Utils\ProductTypes;
use App\Etsy\Models\TaxonomyPropertyCollection;
use App\Etsy\Models\ListingOffering;
use App\Etsy\Models\ListingProduct;
use App\Etsy\Models\PropertyValue;
use App\Etsy\Models\Listing;
use App\GearBubble\Models\PrimaryVariationTaxonomyGroup;
use App\GearBubble\Utils\ListingsFormToListingCollection;

class ListingController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create() {
      return view("shop.gburlform");
    }

/* no longer needed
    // Once the user has input a GB URL, scrape the GB campaign page and display
    // the results to the user. Prompt the user to confirm that this is the correct
    // campaign information, and require that he fill out a few other fields
    // such as tags, description, etc.
    public function confirm() {

      // If the url is passed through the form, use that value. But in the case
      // of a failed validation, the user will be redirected back here. In that case
      // the url is flashed to the session.
      if(!isset(request()->url)) {
        $url = session('url');
      }
      else {
        $url = request()->url;
      }

      // Use a page scraper to gather the information from the GB campaign page.
      $scraper = new PageScraper($url);

      // The scrape() method returns a boolean indicating whether it was successful in scraping the campaign
      if($scraper->scrape()) {

        // The results come back as an array of the campaign data scraped.
        $campaign = $scraper->getCampaign();

        $api = resolve("\App\Etsy\EtsyAPI");

        // Get the shipping templates for this user.
        $shippingTemplates = $api->fetchShippingTemplates(auth()->user()->etsyUserId);

        $results = ["campaign" => $campaign];

        // Insert the shipping templates into the results to pass along to the view.
        $results["shippingTemplates"] = $shippingTemplates;

        $descriptions = Description::where("user_id", auth()->user()->id)->get()->all();
        $results["descriptions"] = $descriptions;

        $taxonomies = $this->taxonomize($campaign->primaryVariations, $campaign);
        $results["taxonomies"] = $taxonomies;

        return view("shop.listingconfirm", $results);
      }
      else {
        $error = "The URL you provided is not a valid campaign or GearBubble is currently inaccessible.";
        return redirect("/listing/create")->withErrors(["error" => $error]);
      }
    }
*/

    public function confirmNew() {
      // If the url is passed through the form, use that value. But in the case
      // of a failed validation, the user will be redirected back here. In that case
      // the url is flashed to the session.
      if(!isset(request()->url)) {
        $url = session('url');
      }
      else {
        $url = request()->url;
      }

      $ps = new PageScraper($url);

      if($ps->scrape()) {
        $c = $ps->getCampaign();
        $lfgc = $c->getFormFieldCollection();
        $data = ["formFieldCollection" => $lfgc, "url" => $url];

        return view("shop.listingconfirmnew", $data);
      }
    }

/*
    // Group the variations by taxonomy. For example, 11 oz and 15 oz mugs have the same taxonomy.
    // But different shirts have different taxonomies (eg. hoodies and t-shirts)
    private function taxonomize($primaryVariations, $campaign) {
      $pt = new ProductTypes();

      $tids = [];
      foreach($primaryVariations as $primaryVariation) {
        $productCode = $primaryVariation->productCode;
        $taxonomyId = $pt->getTaxonomyIdForProductId($productCode);
        if(!isset($tids[$taxonomyId])) {
          $tids[$taxonomyId] = new PrimaryVariationTaxonomyGroup($taxonomyId);
        }
        $tids[$taxonomyId]->addPrimaryVariation($primaryVariation);
        $tids[$taxonomyId]->addImageUrls($campaign->imageUrlsByProductCode[$productCode]);
      }
      return $tids;
    }
*/
    // Submit the new listing to Etsy, get the new listing record back.
    public function submit(Request $request) {
      $lftlc = new ListingsFormToListingCollection($request);
      if(!$lftlc->validates()) {
        return redirect('/listing/confirm')->withErrors(["error" => "All titles and descriptions must be complete"])->with(["url" => $request->url]);
      }
      $lc = $lftlc->getListingCollection();
      for($i = 0; $i < $lc->count(); $i++) {
        $lc->getAt($i)->saveToEtsy();
      }


      // Redirect to the starting point for listing. This does two things:
      // 1. It prevents a refresh from resubmitting and creating a duplicate listing
      // 2. It cycles the user back to list another product. This is the most common use case
      return redirect("/listing/create")->with(["listing" => ""]);
    }

/*
    // Takes a request and returns an array of listing-specific data. For example, a GB shirt
    // campaign may turn into multiple listings (women's tees, hoodies, etc).
    private function extractListingsFromRequest($request) {
      $listings = [];

      // The number of listings is determined by the number of different taxonomies
      // represented in the form data. Look at the taxonomyIds form input value to determine
      // how many taxonomies are represented.
      $taxonomyIds = explode(",", $request->taxonomyIds);

      foreach($taxonomyIds as $taxonomyId) {

        $title = $request[$taxonomyId."_title"];
        $tags = $request[$taxonomyId."_tags"];
        $imageUrls = $request[$taxonomyId."_imageUrls"];
        $description = $request[$taxonomyId."_description"];
        $shippingTemplateId = $request[$taxonomyId."_shippingTemplateId"];

        $productCodes = explode(",", $request[$taxonomyId."_codes"]);
        $firstPrice = $request[$productCodes[0]];

        $listing = new Listing($title, $description, $firstPrice, $taxonomyId, $tags, $shippingTemplateId, $imageUrls);
        array_push($listings, $listing);

        $pt = new ProductTypes();

        $tpc = TaxonomyPropertyCollection::createFromTaxonomyId($taxonomyId);

        $colors = $request[$taxonomyId."_colors"];
        $sizes = explode(",", $request[$taxonomyId."_sizes"]);
        $sizes = $this->mapGBSizesToEtsy($sizes);

          foreach($productCodes as $productCode) {
            $offering = new ListingOffering($request[$productCode]);

            // The property name is the something like "Volume" or "Size". These are specific
            // to Etsy. I need a way to map GB products to Etsy variation types (eg. mugs
            // will have a Volume variation for Etsy). So I'm mapping the variation property name
            // to the product codes in ProductTypes.
            $variationPropertyName = $pt->getVariationPropertyForProductId($productCode);

            // The variation property is a TaxonomyProperty object. I need this to get the
            // property ID to pass to the PropertyValue object.
            $variationProperty = $tpc->propertyByName($variationPropertyName);

            // As with property, I also map Etsy-specific scales to GB product types. A scale
            // is something like "Fluid ounces" or "Milliliters". Scales may have null properties
            // in some cases. For example, when the variation property name is "Style", as in the
            // case of shirts, there is no scale. This is handled gracefully by the rest of the code.
            $scaleName = $pt->getScaleForProductId($productCode);

            // The scale is a TaxonomyPropertyScale object. I need this to get the
            // scale ID to pass to the PropertyValue object.
            $scale = $variationProperty->getScaleByName($scaleName);

            // The value is specific to a product code from GB. For example, GB code
            // 20 is an 11 oz mug. The value is 11. That is because Etsy will need a numeric
            // value. So I'm keeping the values in the ProductTypes map as well.
            $val = $pt->getValueForProductId($productCode);

            // The property value contains the Etsy property ID, the Etsy scale ID, and the value (eg. the number of fluid ounces for a mug)
            $primaryPropVal = new PropertyValue($variationProperty->property_id, $scale->scale_id, [$val]);

            $listing->priceVariationPropertyId = count($productCodes) > 1 ? $primaryPropVal->property_id : null;

            $primaryColorProperty = $tpc->propertyByName("Primary color");
            $sizeProperty = $tpc->propertyByName("Size");

            // For each of the color options, create a new listing product.
            // There will always be at least one color (unless that is not true...in
            // which case, this will break).
            foreach($colors as $color) {

              // If there's just one product code, don't add it as a variation. But
              // if there are more than one product codes, add them as varations.
              $propertyValues = count($productCodes) > 1 ? [$primaryPropVal] : [];

              // Add the color property value to the listing product collection
              // if there are more than one color.
              if(count($colors) > 1) {
                $colorPv = new PropertyValue($primaryColorProperty->property_id, null, [$pt->getColorNameById($color)]);
                array_push($propertyValues, $colorPv);
              }



              if(count($sizes) > 0 && isset($sizeProperty)) {
                $sizeOptions = $sizeProperty->possible_values;

                foreach($sizes as $size) {

                  // map the GB sizes to Etsy size possible values with IDs
                  // because Etsy will only accept two property values with custom
                  // values per product.

                  $pv = $sizeProperty->getPossibleValueByName($size);

                  // $pv can be undefined if the size isn't a possible value. (For example, GB has a 5XL, but Etsy only has possible values up to 4X)
                  // In that case, skip this option.
                  if(isset($pv)) {

                    $sizePv = new PropertyValue($sizeProperty->property_id, $pv->scale_id, null, [$pv->value_id]);

                    // Clone the property values array so as not to change the existing one.
                    $propVals = array_slice($propertyValues, 0);
                    array_push($propVals, $sizePv);

                    $lp = new ListingProduct($propVals, [$offering]);
                    $listing->staging->addProduct($lp);
                  }
                }
              }
              else {
                $lp = new ListingProduct($propertyValues, [$offering]);
                $listing->staging->addProduct($lp);
              }
            }
          }
      }

      return $listings;
    }
*/
/*
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
*/
}
