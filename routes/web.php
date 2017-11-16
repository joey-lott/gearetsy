<?php

use App\User;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

use Goutte\Client;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\UserAccessLevel;

Route::get('/test2', function() {
  $title = "#++^%*&^%)()__ 1234 abcde 123123";
  $title = preg_replace('/^[^a-zA-Z0-9]+(?=[a-zA-Z0-9])/', "", $title);
  dd($title);
});


Route::get('/test', function() {
  $users = User::get()->all();
  foreach($users as $user) {
    $access = new UserAccessLevel();
    $access->user_id = $user->id;
    $access->access_level = "unlimited";
    $access->save();
  }
  dd("done");
  $title = "Demo Shirt - Multiple Styles - Gifts Under 50! - _ - Get the Shirt && :: - 50% off! - 50% off regular price! - This is a really long title";
  $titleChunks = explode("&", $title);
  if(count($titleChunks) > 1) {
     $titleChunks[0] .= "&";
  }
  $title = implode("", $titleChunks);
  dd($title);
//  return view('test');
});
Route::post('/test', function(Request $request) {
//  dump(auth()->user()->apiKey);
dd(preg_replace('/([^a-zA-Z0-9\-\s_\'])*/', "", $request->s));

  // dump(php_ini_loaded_file());
  // dump(php_ini_scanned_files());
  // dd(phpinfo());
  // $client = new Client;
  // $url = 'https://www.pinterest.com/search/pins/?q=boss%20quote';
  // $crawler = $client->request('GET', $url);
  // $json = $crawler->filterXPath('//script[contains(@type, "application/json")]')->extract("_text")[0];
  // $obj = json_decode($json);
  // dd($obj);
  //dd(phpinfo());
});


Route::post('/login', "UserController@login")->name('login');
Route::get('/register', "UserController@create");
Route::post('/register', "UserController@store")->name('register');
Route::get('/apikey', "UserController@etsyApiKey");
Route::post('/apikey', "UserController@etsyApiKeySubmit");
Route::get('/authorize', "UserController@etsyAuthorize");
Route::get('/authorize/complete', "UserController@completeAuthorization")->name('completeAuthorization');

Route::get('dashboard', "HomeController@dashboard");
Route::get('/', 'HomeController@index');
//Route::get('/signin', 'HomeController@signin');
Route::get('/logout', function() {
  auth()->logout();
  return redirect("/");
});

Route::get('/shop/find', 'ShopController@find');
Route::post('/shop/confirm', 'ShopController@confirm');
Route::post('/shop/confirm/store', 'ShopController@confirmStore');
Route::get('/shop/{id}/dashboard', 'ShopController@dashboard');

Route::get('/listing/create', 'ListingController@create');
// Handle both get and post for confirm in the same way. This is because
// if submission validation fails, it redirets with get.
Route::get('/listing/confirm', 'ListingController@confirmNew');
Route::post('/listing/confirm', 'ListingController@confirmNew');
Route::post('/listing/submit', 'ListingController@submit');

Route::get('shippingtemplate/deletecache', 'ShippingTemplateController@deleteCachedTemplateFile');
// Route::get('shippingtemplate/create', 'ShippingTemplateController@create');
// Route::post('shippingtemplate', 'ShippingTemplateController@submit');
// Route::get('shippingtemplate', 'ShippingTemplateController@list');
// Route::get('shippingtemplate/{id}', 'ShippingTemplateController@view');
// Route::post('shippingtemplate/{id}', 'ShippingTemplateController@update');
// Route::get('shippingtemplate/entry/{id}', 'ShippingTemplateController@entryView');

Route::get('description/create', 'DescriptionController@create');
Route::post('description/submit', 'DescriptionController@submit');
Route::post('description/edit', 'DescriptionController@change');
Route::post('description/{id}/delete', 'DescriptionController@delete');
Route::delete('description/{id}/delete', 'DescriptionController@destroy');
Route::get('description/{id}', 'DescriptionController@view');
Route::get('description', 'DescriptionController@index');

Route::get('/admin/api-analytics', "APIAnalyticsController@index");
Route::get('admin', "AdminController@dashboard");
Route::get('/admin/add-provisional', "AdminController@provisionalForm");
Route::post('admin/add-provisional', "AdminController@provisionalSubmit");

Route::get('/instructions', function() {
  return view("help/instructions");
});
