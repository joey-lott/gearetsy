<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
      $credentials = $request->only("email", 'password');
      $response = $this->guard()->attempt($credentials, $request->filled('remember'));
      if($response) { return redirect('dashboard'); }
      else { return redirect(''); }
    }

    public function create() {
      return view('auth.register');
    }

    public function store(Request $request) {
      $this->validate($request, [
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:6|confirmed'
      ]);
      $user = User::create(["email" => $request->email, "password" => bcrypt($request->password)]);
      $this->guard()->login($user);
      return redirect('/authorize');
    }

    public function etsyAuthorize() {
      $user = new User;
      $user->email = "email";
      $user->password = "password";
      $etsyLink = resolve("\App\EtsyAPI")->getEtsyAuthorizeLink();
      return view('auth.authorizeEtsy', ["etsyLink" => $etsyLink]);
    }

    public function completeAuthorization(Request $request) {
      $tokenSecret = $_COOKIE['token_secret'];
      $token = $_GET['oauth_token'];
      $verifier = $_GET['oauth_verifier'];
      $response = resolve("\App\EtsyAPI")->finalizeAuthorization($tokenSecret, $token, $verifier);
      if($response) {
        $user = auth()->user();
        if($user->shopId) {
          // If the shop ID is not set, direct user to select shop
          return redirect("/dashboard");
        }
        else {
          // Otherwise, send the user to the dashboard.
          return redirect("/dashboard")->with("state", "normal");
        }
      }
      else {
        return redirect("/dashboard")->with("state", "etsyAuthRetry");
      }
    }

    private function guard() {
      return \Auth::guard();
    }
}
