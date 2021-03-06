<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ApiCalls;
use App\Http\Middleware\RedirectIfNotAdmin;

class APIAnalyticsController extends Controller
{

  public function __construct() {
    $this->middleware(RedirectIfNotAdmin::class);
  }


    public function index(Request $request) {
//      dd($request->query);
      // today, all time
      $today = \Carbon\Carbon::today();
//      dd($today);
      $calls = ApiCalls::selectRaw("name, COUNT(name) as count")->where("when_called", ">=", $today)->groupBy("name")->orderBy("count", "desc")->get();
//      $calls = $ac->groupBy("name");
      $totalCount = 0;
      foreach($calls as $call) {
        $totalCount += $call->count;
      }
      return view("analytics.index", ["data" => $calls, "totalCount" => $totalCount]);
      //return view("description.index", ["descriptions" => $descriptions]);
    }

}
