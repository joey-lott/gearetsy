<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Description extends Model {

  protected $fillable = ["title", "description", "user_id"];

}
