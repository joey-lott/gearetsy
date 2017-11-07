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
        $this->middleware('auth', ["except" => "index"]);
        $this->middleware('guest', ["only" => "index"]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      return view('home');
    }

    public function dashboard(Request $request) {
      $user = auth()->user();
      $hasApiKey = isset($user->apiKey);
      $hasAuthToken = $user->oauthToken != null;
      $hasShopId = $user->shopId != null;
      // If there is no oauthToken, that means the user has not authorized
      // the app to access his Etsy shop. In that case, redirect to
      // the authorize page. If the user is authorized but has no shop ID
      // stored, redirect to shop/find (which is now automatic, and could probably
      // be folded into the authorization process). Otherwise, display the dashboard
      if($hasAuthToken && $hasShopId && $hasApiKey) {
        return view("dashboard");
      }
      else if(!$hasApiKey) {
        return redirect("/apikey");
      }
      else if(!$hasAuthToken) {
        return redirect("/authorize");
      }
      else {
        return redirect("/shop/find");
      }
    }
}
