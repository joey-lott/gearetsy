<?php

namespace App\GearBubble\Utils;


class ProductTypes {


  private $products = ["20" => ["name" => "mug", "variation" => "11 oz", "scale" => "Fluid ounces"],
                       "43" => ["name" => "mug", "variation" => "15 oz"]
                      ];

  private $categories = ["mugs" => ["scale" => "Fluid ounces", "variationProperty" => "Volume"]
                        ];

  public function getVariationPropertyForCategory($cat) {
    foreach($this->categories as $category => $value) {
      if($category == $cat) {
        return $value["variationProperty"];
      }
    }
  }

}
