<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Shop;

class ShopController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function find()
    {
      $api = resolve("\App\EtsyAPI");
      $shop = $api->fetchShopCurrentUser();
      $shopId = $shop["shop_id"];
      $etsyUserId = $shop["user_id"];
      $user = auth()->user();
      $user->shopId = $shopId;
      $user->etsyUserId = $etsyUserId;
      $user->save();
      return view("shop.shopnameform", ["shopName" => $shop["shop_name"]]);
    }

    public function confirmStore(Request $request) {
      $shopId = session("shopId");
      $userId = session("userId");
      $user = auth()->user();
      $user->shopId = $shopId;
      $user->etsyUserId = $userId;
      $user->save();
      return redirect("dashboard");
    }

    public function confirm(Request $request) {
      // Verify that the shop name has been passed from
      // the form.
      $this->validate($request, [
        "shop_name" => "required"
      ]);

      $shopFromAPI = resolve("\App\EtsyAPI");
      $fetchedShop = $shopFromAPI->fetchShop($request->shop_name);

      // If no shop (consider refactoring to try/catch with fetchShop() throwing exception)
      // display error message to user. Otherwise, show
      if($fetchedShop == "404") {
        dd("Etsy could not find a shop with that name or ID");
      }
      else {
        // store shop ID in session.
        session(["userId" => $fetchedShop->user_id]);
        session(["shopId" => $fetchedShop->shop_id]);
        return view('shop.confirm', ["shopName" => $fetchedShop->shop_name, "url" => $fetchedShop->url, "image" => $fetchedShop->icon_url_fullxfull]);
      }
    }

    public function dashboard($id) {
      $api = resolve("\App\EtsyAPI");
      $listings = $api->fetchListings($id);
      // $shop = Shop::where("name", "=", $id)->first();
      // $listings = $shop->listings();
      dd($listings);
    }

}
