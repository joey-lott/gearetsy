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
        $api = resolve("\App\EtsyAPI");
        $api->createShippingTemplate($request);
    }

    public function list() {
      $api = resolve("\App\EtsyAPI");
      $list = $api->fetchShippingTemplates(auth()->user()->etsyUserId);
      return view("shipping.templatelist", ["list" => $list]);
    }

    public function view($id) {
      $response = $this->api()->fetchShippingTemplateEntries($id);
      return view("shipping.templateview", ["entries" => $response]);
    }

    private function api() {
      return resolve("\App\EtsyAPI");
    }

}
