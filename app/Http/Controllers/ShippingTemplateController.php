<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client as GClient;
use Goutte\Client;
use App\Shop;

class ShippingTemplateController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create() {
      $api = resolve("\App\EtsyAPI");
      $countries = $api->fetchCountries();
      $regions = $api->fetchRegions();
      return view("shipping.newtemplate", ["countries" => $countries, "regions" => $regions]);
    }

    public function submit(Request $request) {
        $this->validate($request, [
          "title" => "required",
          "us_cost" => "numeric",
          "ca_cost" => "numeric",
          "ww_cost" => "numeric",
          "min_production_time" => "numeric",
          "max_production_time" => "numeric"
        ]);
        $api = resolve("\App\EtsyAPI");
        $api->createShippingTemplate($request);
        return redirect("/shippingtemplate")->with(["message" => "Shipping template created successfully"]);
    }

    public function update($id, Request $request) {
        $this->validate($request, [
          "title" => "required",
          "us_cost" => "numeric",
          "ca_cost" => "numeric",
          "ww_cost" => "numeric",
          "min_production_time" => "numeric",
          "max_production_time" => "numeric",
        ]);
        $api = resolve("\App\EtsyAPI");
        $api->updateShippingTemplate($id, $request);
        return redirect("/shippingtemplate")->with(["message" => "Shipping template updated successfully"]);
    }

    public function list() {
      $api = resolve("\App\EtsyAPI");
      $list = $api->fetchShippingTemplates(auth()->user()->etsyUserId);
      return view("shipping.templatelist", ["list" => $list]);
    }

    public function view($id) {
      $template = $this->api()->fetchShippingTemplateById($id);
      $entries = $this->api()->fetchShippingTemplateEntries($id);

      foreach($entries as $entry) {
        switch($entry["destination_country_id"]) {
          case "209":
            // USA - hardcoding for now. This is a value from Etsy. But unlikely to change.
            $us_entry = (object) $entry;
            break;
          case "79":
            $ca_entry = (object) $entry;
            break;
          default:
            $ww_entry = (object) $entry;
        }
      }
      if(!(isset($us_entry) && isset($ca_entry) && isset($ww_entry))) {
        return back()->with(["message" => "The shipping template you selected is not compatible with GearEtsy. You must edit that template from your Etsy shop manager."]);
      }
      return view("shipping.templateview", ["us_entry" => $us_entry, "ca_entry" => $ca_entry, "ww_entry" => $ww_entry, "template" => $template]);
    }

    private function api() {
      return resolve("\App\EtsyAPI");
    }

}
