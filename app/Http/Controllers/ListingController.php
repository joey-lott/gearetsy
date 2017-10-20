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

class ListingController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create() {
      return view("shop.gburlform");
    }

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
//dd($url);
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

/*    private function findTaxonomyIdForNamedTaxonomy($taxonomy, $name) {
      foreach($taxonomy as $item) {
        echo($item->name." ".$item->id."<br>");
        if($item->name == $name) {
          return $item->id;
        }
        if(isset($item->children)) {
          $id = $this->findTaxonomyIdForNamedTaxonomy($item->children, $name);
          if(isset($id)) {
            return $id;
          }
        }
      }
    }
*/
/* no longer needed
    private function extractFirstPrice($request) {
      $productCodes = explode(",", $request->codes);
      $firstPrice = $request[$productCodes[0]];
      return $firstPrice;
    }
    */

    // Submit the new listing to Etsy, get the new listing record back.
    public function submit(Request $request) {

      $taxonomyIds = explode(",", $request->taxonomyIds);
      foreach($taxonomyIds as $taxonomyId) {
        $validator = validator()->make($request->all(), [
          $taxonomyId."_title" => "required",
          $taxonomyId."_description" => "required",
        ]);
        if($validator->fails()) {
          return redirect('/listing/confirm')->withErrors(["error" => "All titles and descriptions must be complete"])->with(["url" => $request->url]);
        }
      }
      // Should add a working validator. Had to comment this one out once I
      // made the campaign->listing mapping more complicated
      /*$validator = validator()->make($request->all(), [
        "title" => "required",
        "description" => "required",
        "colors" => "required"
      ]);
      if($validator->fails()) {
        return redirect()->back()->withErrors($validator)->with(["url" => $request->url]);
      }*/
      $listings = $this->extractListingsFromRequest($request);

//      $listing = $this->addNewListing($request->title, $request->description, $this->extractFirstPrice($request), $request->taxonomy_id, $request->tags, $request->shippingTemplateId, $request->imageUrls, $request->codes, $request);
      foreach($listings as $listing) {
        $listing->saveToEtsy();
      }


      // Redirect to the starting point for listing. This does two things:
      // 1. It prevents a refresh from resubmitting and creating a duplicate listing
      // 2. It cycles the user back to list another product. This is the most common use case
      return redirect("/listing/create")->with(["listing" => $listing]);
    }

    // Takes a request and returns an array of listing-specific data. For example, a GB shirt
    // campaign may turn into multiple listings (women's tees, hoodies, etc).
    private function extractListingsFromRequest($request) {
      $listings = [];

      // The number of listings is determined by the number of different taxonomies
      // represented in the form data. Look at the taxonomyIds form input value to determine
      // how many taxonomies are represented.
      $taxonomyIds = explode(",", $request->taxonomyIds);

      foreach($taxonomyIds as $taxonomyId) {
//echo("taxonomy id: ".$taxonomyId."<br>");
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
//dd($tpc);
        $colors = $request[$taxonomyId."_colors"];
        $sizes = explode(",", $request[$taxonomyId."_sizes"]);
        $sizes = $this->mapGBSizesToEtsy($sizes);
//echo("product codes: ".count($productCodes)."<br>");

          foreach($productCodes as $productCode) {
            $offering = new ListingOffering($request[$productCode]);

            // The property name is the something like "Volume" or "Size". These are specific
            // to Etsy. I need a way to map GB products to Etsy variation types (eg. mugs
            // will have a Volume variation for Etsy). So I'm mapping the variation property name
            // to the product codes in ProductTypes.
            $variationPropertyName = $pt->getVariationPropertyForProductId($productCode);
//dd($tpc);
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
//echo("color: ".$color."<br>");

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
//dd($listings);
      return $listings;
    }

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

/* no longer used
    private function addNewListing($title, $description, $price, $taxonomyId, $tags, $shippingTemplateId, $imageUrls, $codes, $primaryVariation, $request) {

      $listing = new Listing($title, $description, $price, $taxonomyId, $tags, $shippingTemplateId, $imageUrls);
      $listing = $listing->saveToEtsy();

      // The codes hidden field is only generated in the case of variations. So
      // test for its existence. And if so, create variations.
      if(isset($codes)) {


        $pt = new ProductTypes();

        $taxonomyId = $pt->getTaxonomyIdForProductId($primaryVariation["productCode"]);

        $tpc = TaxonomyPropertyCollection::createFromTaxonomyId($listing["taxonomy_id"]);

        $products = [];

        // The product codes are GB-specific codes pass through from the results of the screen scraping.
        // The form has a hidden field with a comma-delimited list of the codes. Split them into an array here.
        $productCodes = explode(",", $codes);

        // There is one product code for each variation. For example, 20 and 43 are white mugs, 11 oz and 15 oz. So
        // for each product code there will be one variation.
        foreach($productCodes as $productCode) {

          // The property name is the something like "Volume" or "Size". These are specific
          // to Etsy. I need a way to map GB products to Etsy variation types (eg. mugs
          // will have a Volume variation for Etsy). So I'm mapping the variation property name
          // to the product codes in ProductTypes.
          $variationPropertyName = $pt->getVariationPropertyForProductId($productCode);

          // As with property, I also map Etsy-specific scales to GB product types. A scale
          // is something like "Fluid ounces" or "Milliliters". Scales may have null properties
          // in some cases. For example, when the variation property name is "Style", as in the
          // case of shirts, there is no scale. This is handled gracefully by the rest of the code.
          $scaleName = $pt->getScaleForProductId($productCode);

          // The value is specific to a product code from GB. For example, GB code
          // 20 is an 11 oz mug. The value is 11. That is because Etsy will need a numeric
          // value. So I'm keeping the values in the ProductTypes map as well.
          $val = $pt->getValueForProductId($productCode);

          // The price for each variation is determined by what the user inputs in the form.
          // The form inputs are named the same as the product codes. So I can grab the value
          // from the $request.
          $variationPrice = $request[$productCode];

          // An offering contains the price of the variation.
          $offering = new ListingOffering($variationPrice);

          // The variation property is a TaxonomyProperty object. I need this to get the
          // property ID to pass to the PropertyValue object.
          $variationProperty = $tpc->propertyByName($variationPropertyName);

          // The scale is a TaxonomyPropertyScale object. I need this to get the
          // scale ID to pass to the PropertyValue object.
          $scale = $variationProperty->getScaleByName($scaleName);

          // The property value contains the Etsy property ID, the Etsy scale ID, and the value (eg. the number of fluid ounces for a mug)
          $pimaryPropVal = new PropertyValue($variationProperty->property_id, $scale->scale_id, [$val]);

          $primaryColorProperty = $tpc->propertyByName("Primary color");

          // For each color variation, create a combination offer. For example, white 11 oz, white 15 oz, black 11 oz, black 15 oz.
          foreach($request->colors as $color) {

            // The listing product contains the property value and the offering.
            $listingProduct = new ListingProduct([$primaryPropVal], [$offering]);

            // Add the listing product to the array of variations. This is what I'll pass to Etsy.
            array_push($products, $listingProduct);

          }

        }

        // Etsy does not have a method for creating inventory. But once can update the inventory for an existing listing.
        // Here, pass the json-encoded array of products to the updateInventory method along with the listing ID and the ID of
        // the property that varies the price on the listing. I'm assuming that there is only one variation for now. In the
        // future, there may be multiple variations, and I'll need to handle this better. (For example, shirts can have
        // variations in size, color, and style)

        $api = resolve("\App\Etsy\EtsyAPI");
        $response = $api->updateInventory($listing["listing_id"], json_encode($products), $variationProperty->property_id);

      }
      return $listing;
    }
*/
}
