<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client as GClient;
use Goutte\Client;
use App\Description;

class DescriptionController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index() {
      $descriptions = Description::where("user_id", auth()->user()->id)->get()->all();
      return view("description.index", ["descriptions" => $descriptions]);
    }

    public function view($id) {
      $description = Description::where("id", "=", $id)->first();
      return view("description.view", ["description" => $description]);
    }

    public function change() {
      $request = request();
      $this->validate($request, [
        "title" => "required",
        "description" => "required"
      ]);
      $description = Description::where("id", "=", $request->id)->first();
      $description->title = $request->title;
      $description->description = $request->description;
      $description->save();
      return redirect("/description")->with(["message" => "Description Changed"]);
    }

    public function create() {
      return view("description.create");
    }

    public function submit() {
      $request = request();
      $this->validate($request, [
        "title" => "required",
        "description" => "required"
      ]);
      $description = Description::create(["title" => $request->title,
                                          "description" => $request->description,
                                          "user_id" => auth()->user()->id]);
      $description->save();
      return redirect("/description")->with(["message" => "Description Created"]);
    }

    public function delete($id) {
      return view("description.delete", ["id" => $id]);
    }

    public function destroy($id) {
      Description::destroy($id);
      return redirect("/description")->with(["message" => "Description Deleted"]);
    }

}
