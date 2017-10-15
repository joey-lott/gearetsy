<?php

namespace App\Etsy\Models;

class TaxonomyPropertyScale {

  public $scale_id;
  public $display_name;
  public $description;

  static public function createFromAPIResponse($response) {
    $tps = new TaxonomyPropertyScale($response["scale_id"], $response["display_name"], $response["description"]);
    return $tps;
  }

  public function __construct($id, $name, $d) {
    $this->scale_id = $id;
    $this->display_name = $name;
    $this->description = $d;
  }

}
