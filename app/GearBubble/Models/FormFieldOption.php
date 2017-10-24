<?php
namespace App\GearBubble\Models;

class FormFieldOption {

  public $value;
  public $label;

  public function __construct($value, $label) {
    $this->value = $value;
    $this->label = $label;
  }

}
