<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Host;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $etsyLink = resolve("\App\EtsyAPI")->getEtsyAuthorizeLink();
      return view('home', ["etsyLink" => $etsyLink]);
      // $client = new Client;
      // $response = $client->get("https://openapi.etsy.com/v2/shops/realpeoplegoods/listings/active?api_key=73aeqt6bwe0s6mcqujdc1rrz");
      // return $response;
    }

    public function signin(Request $request) {
      //return $request;
    }

    public function dashboard(Request $request) {
      $user = auth()->user();
      $hasAuthToken = $user->oauthToken != null;
      $hasShopId = $user->shopId != null;
      if($hasAuthToken && $hasShopId) {
        //resolve("\App\EtsyAPI")->uploadImage("550543222", "https://gearbubble-assets.s3.amazonaws.com/5/1699738/43/235/front.png");
        //resolve("\App\EtsyAPI")->uploadImage("550543222", "front.png");
        return view("dashboard");
      }
      else if(!$hasAuthToken) {
        return redirect("/authorize");
      }
      else {
        return redirect("/shop/find");
      }
      dd(auth()->user()->oauthToken);
      return view("dashboard");
    }
}
