<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAccessLevel extends Model
{
    static public function hasUnlimitedAccess($userId) {
      $accessLevel = self::where("user_id", $userId)->pluck("access_level")->first();
      return $accessLevel === "unlimited";
    }
}
