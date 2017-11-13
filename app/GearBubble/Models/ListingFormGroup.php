<?php

namespace App\GearBubble\Models;

use App\GearBubble\Models\FormFieldOption;

class ListingFormGroup {

  public $title;
  public $primaryVariations;
  public $tags;
  public $description;
  public $imageUrls;
  public $colors;
  public $shippingTemplate;
  public $sizes;
  public $stagingId;
  public $shippingTemplates;
  public $descriptions;
  public $primaryVariationCodes;

  public $fieldOrder;

  public function __construct($listing, $descriptions, $shippingTemplates) {
    $this->stagingId = $sid = $listing->staging->stagingId;
    $this->title = new ListingFormItem("title", "title_".$sid, "text", $listing->title);
    $this->imageUrls = new ListingFormItem("images", "imageUrls_".$sid, "imageSelect", null, $this->mapToOptions($listing->imagesToAddFromUrl));
    $this->colors = new ListingFormItem("colors", "colors_".$sid, "checkBoxGroup", null, $this->mapToOptions($listing->staging->colors, "id", "label"));
    $this->sizes = new ListingFormItem("sizes", "sizes_".$sid, "hidden", implode(",", $listing->staging->sizes));
    $this->taxonomyId = new ListingFormItem("title", "taxonomyId_".$sid, "hidden", $listing->taxonomy_id);
    $this->primaryVariations = $this->createPrimaryVariationFormItemsArray($listing->staging->primaryVariations);
    $this->tags = new ListingFormItem("tags", "tags_".$sid, "text", "");
    $descDefault = count($descriptions) ? $descriptions[0]->description : "";
    $this->description = new ListingFormItem("description", "description_".$sid, "textarea", $descDefault);
    // Set the descriptions default value to the first of the primary variations
//    dd($listing->staging->primaryVariations);
//dd($descriptions);
    if(resolve("\App\DebugFlag")->debug) {
      dump("all descriptions:");
      dump($descriptions);
    }
    $this->descriptions = new ListingFormItem("descriptions", "descriptions_".$sid, "select", $listing->staging->primaryVariations[0]->description, $this->mapToOptions($descriptions, "description", "title"), "description_".$sid);
    $this->shippingTemplates = new ListingFormItem("shipping templates", "shippingTemplate_".$sid, "select", "", $this->mapToOptions($shippingTemplates, "shipping_template_id", "title"));
    $this->primaryVariationCodes = new ListingFormItem("primaryVariationCodes", "primaryVariationsCodes_".$sid, "hidden", $this->createArrayToString($listing->staging->primaryVariations, "productCode"));

    $this->fieldOrder = [$this->title, $this->taxonomyId, $this->tags, $this->imageUrls, $this->sizes, $this->colors, $this->descriptions, $this->description, $this->shippingTemplates, $this->primaryVariationCodes];
    foreach($this->primaryVariations as $pv) {
      array_push($this->fieldOrder, $pv);
    }
  }

  private function createArrayToString($input, $field) {
    $a = [];
    foreach($input as $i) {
      array_push($a, $i->$field);
    }
    return implode(",", $a);
  }

  private function mapToOptions($input, $idField = null, $labelField = null) {
    $options = [];
    foreach($input as $i) {
      $id = (isset($idField)) ? ((object) $i)->$idField : $i;
      $label = (isset($labelField)) ? ((object) $i)->$labelField : $i;
      array_push($options, new FormFieldOption($id, $label));
    }
    return $options;
  }

  private function createPrimaryVariationFormItemsArray($pvs) {
    $variations = [];
    foreach($pvs as $pv) {
      array_push($variations, new ListingFormItem($pv->description, "primaryVariation_".$pv->productCode, "text", $pv->price));
    }
    return $variations;
  }
}
