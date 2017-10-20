<?php

namespace App\GearBubble\Utils;


class ProductTypes {


  private $products = ["20" => ["category" => "mugs", "variation" => "11 oz", "value" => "11", "taxonomyId" => "1062"],
                       "43" => ["category" => "mugs", "variation" => "15 oz", "value" => "15", "taxonomyId" => "1062"],
                       "62" => ["category" => "mugs", "variation" => "Travel Mug", "value" => "Travel Mug", "taxonomyId" => "1062"],
                       "22" => ["category" => "shirts", "variation" => "Unisex Tee", "value" => "Unisex Tee", "taxonomyId" => "482"],
                       "28" => ["category" => "shirts", "variation" => "Women's Tee", "value" => "Women's Tee", "taxonomyId" => "559"],
                       "35" => ["category" => "shirts", "variation" => "Youth Tee", "value" => "Youth Tee", "taxonomyId" => "498"],
                       "29" => ["category" => "shirts", "variation" => "Hoodie", "value" => "Hoodie", "taxonomyId" => "1853"],
                       "57" => ["category" => "shirts", "variation" => "Long Sleeve Tee", "value" => "Long Sleeve Tee", "taxonomyId" => "482"],
                       "58" => ["category" => "leggings", "variation" => "Leggings", "value" => "Leggings", "taxonomyId" => "510"],
                       "31" => ["category" => "necklaces", "variation" => "Circular Silver", "value" => "Circular Silver", "taxonomyId" => "1229"],
                       "52" => ["category" => "necklaces", "variation" => "Circular Gold", "value" => "Circular Gold", "taxonomyId" => "1229"],
                       "32" => ["category" => "necklaces", "variation" => "Square Silver", "value" => "Square Silver", "taxonomyId" => "1229"],
                       "51" => ["category" => "necklaces", "variation" => "Square Gold", "value" => "Square Gold", "taxonomyId" => "1229"],
                       "37" => ["category" => "necklaces", "variation" => "Heart-Shaped Silver", "value" => "Heart-Shaped Silver", "taxonomyId" => "1229"],
                       "53" => ["category" => "necklaces", "variation" => "Heart-Shaped Gold", "value" => "Heart-Shaped Gold", "taxonomyId" => "1229"],
                       "59" => ["category" => "necklaces", "variation" => "Gold Engraved", "value" => "Gold Engraved", "taxonomyId" => "1229"],
                       "60" => ["category" => "necklaces", "variation" => "Stainless Steel Engraved", "value" => "Stainless Steel Engraved", "taxonomyId" => "1229"]
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


  private $categories = ["mugs" => ["scale" => "Fluid ounces", "variationProperty" => "Volume", "imageCount" => "2"],
                         "shirts" => ["scale" => "", "variationProperty" => "Style", "imageCount" => "2"],
                         "leggings" => ["scale" => "", "variationProperty" => "Style", "imageCount" => "2"],
                         "necklaces" => ["scale" => "", "variationProperty" => "Style", "imageCount" => "1"],
                        ];

  private $colorMap = ["115" => "White",
                       "116" => "Black",
                       "149" => "Red",
                       "150" => "Royal Blue",
                       "153" => "Purple",
                       "154" => "Heather",
                       "155" => "Navy Blue",
                       "156" => "Pink",
                       "251" => "Green",
                       "270" => "Gold"
                     ];

  private $sizeMap = ["sml" => "S",
                      "med" => "M",
                      "lrg" => "L",
                      "xlg" => "XL",
                      "xxl" => "2X",
                      "xxxl" => "3X",
                      "xxxxl" => "4X"];

  public function mapSize($gb) {
    if(isset($this->sizeMap[$gb])) {
      return $this->sizeMap[$gb];
    }
    return null;
  }

  public function getColorNameById($id) {
    foreach($this->colorMap as $cid => $name) {
      if($cid == $id) {
        return $name;
      }
    }
    return "Uknown Color (Update colorMap in ProductTypes)";
  }

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

  public function getDisplayNameForProductId($id) {
    return $this->products[$id]["variation"];
  }

  public function hasMultipleImages($id) {
    $category = $this->getCategoryForProductId($id);
    return $this->categories[$category]["imageCount"] > 1;
  }

}
