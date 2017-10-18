<?php

namespace App\GearBubble\Utils;


class ProductTypes {


  private $products = ["20" => ["category" => "mugs", "variation" => "11 oz", "value" => "11"],
                       "43" => ["category" => "mugs", "variation" => "15 oz", "value" => "15"],
                       "22" => ["category" => "shirts", "variation" => "Unisex Tee", "value" => "22"],
                       "28" => ["category" => "shirts", "variation" => "Women's Tee", "value" => "28"],
                       "35" => ["category" => "shirts", "variation" => "Youth Tee", "value" => "35"],
                      ];

  private $categories = ["mugs" => ["scale" => "Fluid ounces", "variationProperty" => "Volume"],
                         "shirts" => ["scale" => "Fluid ounces", "variationProperty" => "Volume"],
                        ];

  public function getVariationPropertyForCategoryName($cat) {
    foreach($this->categories as $category => $value) {
      if($category == $cat) {
        return $value["variationProperty"];
      }
    }
  }

  public function getScaleForCategoryName($cat) {
    foreach($this->categories as $category => $value) {
      if($category == $cat) {
        return $value["scale"];
      }
    }
  }

  public function getCategoryForProductId($id) {
    foreach($this->products as $productId => $data) {
      if($productId == $id) {
        return $data["category"];
      }
    }
  }

  public function getVariationPropertyForProductId($id) {
    $category = $this->getCategoryForProductId($id);
    return $this->getVariationPropertyForCategoryName($category);
  }

  public function getScaleForProductId($id) {
    $category = $this->getCategoryForProductId($id);
    return $this->getScaleForCategoryName($category);
  }

  public function getValueForProductId($id) {
    foreach($this->products as $productId => $data) {
      if($productId == $id) {
        return $data["value"];
      }
    }
  }

}
