<?php

namespace App\Http\Controllers;

use App\Http\Middleware\RedirectIfNotAdmin;
use Illuminate\Http\Request;

class AdminController extends Controller
{

  public function __construct() {
    $this->middleware(RedirectIfNotAdmin::class);
  }

  public function dashboard() {
    return view("admin.dashboard");
  }

  public function provisionalForm() {
    return view("admin.provisionalForm");
  }

  public function provisionalSubmit(Request $request) {
    $this->validate($request, [
      "shopName" => "required"
    ]);
    $api = resolve("\App\Etsy\EtsyAPI");
    $response = $api->fetchShop($request->shopName);
    $response = $api->addProvisionalUser($response->user_id);
    return "added provisional user successfully";
  }
}
