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
use App\Etsy\Models\ListingInventory;
use App\Etsy\Models\ListingProduct;
use App\Etsy\Models\PropertyValue;
use App\Etsy\Models\Listing;

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

      // Use a page scraper to gather the information from the GB campaign page.
      $scraper = new PageScraper($url);

      // The scrape() method returns a boolean indicating whether it was successful in scraping the campaign
      if($scraper->scrape()) {

        // The results come back as an array of the campaign data scraped.
        $results = $scraper->getResults();

        $api = resolve("\App\Etsy\EtsyAPI");

        // Get the shipping templates for this user.
        $shippingTemplates = $api->fetchShippingTemplates(auth()->user()->etsyUserId);

        // Insert the shipping templates into the results to pass along to the view.
        $results["shippingTemplates"] = $shippingTemplates;

        $taxonomies = $this->taxonomize($results["primaryVariations"]);
        $results["taxonomies"] = $taxonomies;

        return view("shop.listingconfirm", $results);
      }
      else {
        $error = "The URL you provided is not a valid campaign or GearBubble is currently inaccessible.";
        return redirect()->back()->withErrors(["error" => $error]);
      }
    }

    private function taxonomize($primaryVariations) {
      $pt = new ProductTypes();

      $tids = [];
      foreach($primaryVariations as $primaryVariation) {
        $productCode = $primaryVariation->productCode;
        $taxonomyId = $pt->getTaxonomyIdForProductId($productCode);
        if(!isset($tids[$taxonomyId])) {
          $tids[$taxonomyId] = [];
        }
        array_push($tids[$taxonomyId], $primaryVariation);
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

    private function extractFirstPrice($request) {
      $productCodes = explode(",", $request->codes);
      $firstPrice = $request[$productCodes[0]];
      return $firstPrice;
    }

    // Submit the new listing to Etsy, get the new listing record back.
    public function submit(Request $request) {
      $validator = validator()->make($request->all(), [
        "title" => "required",
        "description" => "required",
        "colors" => "required"
      ]);
      if($validator->fails()) {
        return redirect()->back()->withErrors($validator)->with(["url" => $request->url]);
      }

      $listing = $this->addNewListing($request->title, $request->description, $this->extractFirstPrice($request), $request->taxonomy_id, $request->tags, $request->shippingTemplateId, $request->imageUrls, $request->codes, $request);



      // Redirect to the starting point for listing. This does two things:
      // 1. It prevents a refresh from resubmitting and creating a duplicate listing
      // 2. It cycles the user back to list another product. This is the most common use case
      return redirect("/listing/create")->with(["listing" => $listing]);
    }

    private function addNewListing($title, $description, $price, $taxonomyId, $tags, $shippingTemplateId, $imageUrls, $codes, $primaryVariation, $request) {

      $listing = new Listing($title, $description, $price, $taxonomyId, $tags, $shippingTemplateId, $imageUrls);
      $listing = $listing->saveToEtsy();

      // The codes hidden field is only generated in the case of variations. So
      // test for its existence. And if so, create variations.
      if(isset($codes)) {


/* this is copied and pasted here...need to modify to work. Get taxonomy id for product type*/
        $pt = new ProductTypes();

        $taxonomyId = $pt->getTaxonomyIdForProductId($primaryVariation["productCode"]);

        $tpc = TaxonomyPropertyCollection::createFromTaxonomyId($listing["taxonomy_id"]);

        $pt = new ProductTypes;

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

            // The property value contains the Etsy property ID, the Etsy scale ID, and the value (eg. the number of fluid ounces for a mug)
            $pimaryPropVal = new PropertyValue($variationProperty->property_id, $scale->scale_id, [$val]);

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

}
