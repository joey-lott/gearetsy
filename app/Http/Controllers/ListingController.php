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

      if(!isset(request()->url)) {
        $url = session('url');
      }
      else {
        $url = request()->url;
      }

      $scraper = new PageScraper($url);
      if($scraper->scrape()) {
        $results = $scraper->getResults();

        $api = resolve("\App\EtsyAPI");
        $shippingTemplates = $api->fetchShippingTemplates(auth()->user()->etsyUserId);

        $taxonomy = $api->fetchSellerTaxonomy();

        // Get the taxonomy for Mugs. Passing this single value to the view for now.
        // Basically, hard coding for mugs only. Will support multiple taxonomies in the future.
        $taxonomyId = $this->findMugTaxonomyId($taxonomy);

        $results["taxonomy"] = $taxonomyId;
        $results["shippingTemplates"] = $shippingTemplates;
        return view("shop.listingconfirm", $results);
      }
      else {
        $error = "The URL you provided is not a valid campaign or GearBubble is currently unaccessible.";
      }
      return redirect()->back()->withErrors(["error" => $error]);
    }

    private function findMugTaxonomyId($taxonomy) {
      foreach($taxonomy as $item) {
        if($item->name == "Mugs") {
          return $item->id;
        }
        if(isset($item->children)) {
          $mugId = $this->findMugTaxonomyId($item->children);
          if(isset($mugId)) {
            return $mugId;
          }
        }
      }
    }

    public function submit(Request $request) {
      $validator = validator()->make($request->all(), [
        "title" => "required",
        "price" => "required|numeric",
        "description" => "required"
      ]);
      if($validator->fails()) {
        return redirect()->back()->withErrors($validator)->with(["url" => $request->url]);
      }
      $api = resolve("\App\EtsyAPI");
      $listing = $api->createListing($request);

      // The codes hidden field is only generated in the case of variations. So
      // test for its existence. And if so, create variations.
      if(isset($request->codes)) {

        // Get the taxonomy properties for this new listing. This will supply
        // all the variation possibilities for the type of product. To start, I'm
        // only going to support mugs. But this will allow me to support other types of
        // products as well in the near future.
        $tprops = $api->fetchTaxonomyProperties($listing["taxonomy_id"]);
        $tpc = TaxonomyPropertyCollection::createFromAPIResponse($tprops);

        $pt = new ProductTypes;
        dd($pt->getVariationPropertyForCategory("mugs"));

        // For now, I'll only support mug variations. And specifically, I'm only going to
        // support volume variations.
        $volume = $tpc->propertyByName("Volume");

        // Volume for mugs is by fluid ounces. So we're only interested in that for now.
        $ozScale = $volume->getScaleByName("Fluid ounces");

        // For now, just support two volume variations: 11 oz and 15 oz.
        $var11oz = new PropertyValue($volume->name, $ozScale->scale_id, [11]);
        $var15oz = new PropertyValue($volume->name, $ozScale->scale_id, [15]);

        //$o11oz = new ListingOffering()

        dd($response->variations);
      }
      // Redirect to the starting point for listing. This does two things:
      // 1. It prevents a refresh from resubmitting and creating a duplicate listing
      // 2. It cycles the user back to list another product. This is the most common use case
      return redirect("/listing/create")->with(["listing" => $listing]);
    }

}
