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

Route::get('/test', function() {
  dd(phpinfo());
});

Route::post('/login', "UserController@login")->name('login');
Route::get('/register', "UserController@create");
Route::post('/register', "UserController@store")->name('register');
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
Route::get('/listing/confirm', 'ListingController@confirm');
Route::post('/listing/confirm', 'ListingController@confirm');
Route::post('/listing/submit', 'ListingController@submit');

Route::get('shippingtemplate/create', 'ShippingTemplateController@create');
Route::post('shippingtemplate', 'ShippingTemplateController@submit');
Route::get('shippingtemplate', 'ShippingTemplateController@list');
Route::get('shippingtemplate/{id}', 'ShippingTemplateController@view');
Route::post('shippingtemplate/{id}', 'ShippingTemplateController@update');
Route::get('shippingtemplate/entry/{id}', 'ShippingTemplateController@entryView');

Route::get('description/create', 'DescriptionController@create');
Route::post('description/submit', 'DescriptionController@submit');
Route::post('description/edit', 'DescriptionController@change');
Route::post('description/{id}/delete', 'DescriptionController@delete');
Route::delete('description/{id}/delete', 'DescriptionController@destroy');
Route::get('description/{id}', 'DescriptionController@view');
Route::get('description', 'DescriptionController@index');
