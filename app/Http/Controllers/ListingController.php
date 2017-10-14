<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client as GClient;
use Goutte\Client;
use App\Shop;
use App\Description;
use App\GearBubble\PageScraper;

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

        $results["shippingTemplates"] = $shippingTemplates;
        return view("shop.listingconfirm", $results);
      }
      else {
        $error = "The URL you provided is not a valid campaign or GearBubble is currently unaccessible.";
      }
      return redirect()->back()->withErrors(["error" => $error]);
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

      // Redirect to the starting point for listing. This does two things:
      // 1. It prevents a refresh from resubmitting and creating a duplicate listing
      // 2. It cycles the user back to list another product. This is the most common use case
      return redirect("/listing/create")->with(["listing" => $listing]);
    }

}
