<?php

namespace App\GearBubble\Utils;


class ProductTypes {


  private $products = ["20" => ["category" => "mugs", "variation" => "11 oz", "value" => "11", "taxonomyId" => "1062"],
                       "43" => ["category" => "mugs", "variation" => "15 oz", "value" => "15", "taxonomyId" => "1062"],
                       "22" => ["category" => "shirts", "variation" => "Unisex Tee", "value" => "Unisex Tee", "taxonomyId" => "482"],
                       "28" => ["category" => "shirts", "variation" => "Women's Tee", "value" => "Women's Tee", "taxonomyId" => "559"],
                       "35" => ["category" => "shirts", "variation" => "Youth Tee", "value" => "Youth Tee", "taxonomyId" => "498"],
                       "29" => ["category" => "shirts", "variation" => "Hoodie", "value" => "Hoodie", "taxonomyId" => "1062"],
                       "57" => ["category" => "shirts", "variation" => "Long Sleeve Tee", "value" => "Long Sleeve Tee", "taxonomyId" => "482"],
                       "31" => ["category" => "necklaces", "variation" => "Circular Pendant", "value" => "Circular", "taxonomyId" => "482"]
                      ];
/*
mugs (includes travel mugs) 1062
shot glasses 1068
unisex adult hoodies & sweatshirts 469
hoodies 1853
sweatshirts 2202
t-shirts 482
unisex kids' hoodies and sweatshirts 490
hoodies 1854
sweatshirts 2203
tops 498
women's t-shirts 559
pendant necklace 1229
pillowcases 1925
leggings 510
*/


  private $categories = ["mugs" => ["scale" => "Fluid ounces", "variationProperty" => "Volume"],
                         "shirts" => ["scale" => "", "variationProperty" => "Style"],
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

  public function getTaxonomyIdForProductId($id) {
    foreach($this->products as $productId => $data) {
      if($productId == $id) {
        return $data["taxonomyId"];
      }
    }
  }

}
