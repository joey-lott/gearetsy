<?php

namespace App\GearBubble\Models;

class ListingFormItem {

  public $label;
  public $id;
  public $value;
  public $options;
  public $type;
  public $onchangeTarget;

  public function __construct($label, $id, $type, $value, $options = null, $onchangeTarget = null) {
    $this->label = $label;
    $this->id = $id;
    $this->value = $value;
    $this->type = $type;
    $this->options = $options;
    $this->onchangeTarget = $onchangeTarget;
    if(resolve("\App\DebugFlag")->debug) {
      dump("options for $label:");
      dump($options);
    }
  }

}
