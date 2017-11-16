<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ListingThrottle extends Model
{
    static public function clearAllPreviousDays($userId, $today) {
      self::where("user_id", $userId)->where("created_at", "<", $today)->delete();
    }
}
