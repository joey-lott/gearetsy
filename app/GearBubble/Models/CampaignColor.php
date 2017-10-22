<?php
namespace App\GearBubble\Models;

class CampaignColor {

  public $id;
  public $label;

  public function __construct($id, $label) {
    $this->id = $id;
    $this->label = $label;
  }

}
