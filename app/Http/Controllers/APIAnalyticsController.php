<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\APICalls;

class APIAnalyticsController extends Controller
{

    public function __construct()
    {
    }


    public function index(Request $request) {
//      dd($request->query);
      // today, all time
      $today = \Carbon\Carbon::today();
//      dd($today);
      $ac = APICalls::where("when_called", ">=", $today)->get();
      $calls = $ac->groupBy("name");

//      dd($calls);
      return view("analytics.index", ["data" => $calls, "totalCount" => count($ac)]);
      //return view("description.index", ["descriptions" => $descriptions]);
    }

}
