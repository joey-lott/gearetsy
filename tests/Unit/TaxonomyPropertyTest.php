<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Etsy\Models\TaxonomyProperty;
use App\Etsy\Models\TaxonomyPropertyCollection;
use App\Etsy\Models\TaxonomyPropertyPossibleValue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;

class TaxonomyPropertyTest extends TestCase
{

    public function test_can_create_taxonomy_property_collection_from_json()
    {
      $json = Storage::get("taxonomy_properties_test_data.json");
      $tp = TaxonomyPropertyCollection::createFromAPI($json);
      $this->assertEquals(get_class($tp), TaxonomyPropertyCollection::class);
    }

    public function test_collection_has_correct_number_of_taxonomy_properties()
    {
      $json = Storage::get("taxonomy_properties_test_data.json");
      $tp = TaxonomyPropertyCollection::createFromAPI($json);
      $this->assertEquals($tp->count(), 23);
    }

    public function test_collection_taxonomy_properties_are_correct_type()
    {
      $json = Storage::get("taxonomy_properties_test_data.json");
      $tp = TaxonomyPropertyCollection::createFromAPI($json);
      $this->assertEquals(get_class($tp->getAt(0)), TaxonomyProperty::class);
    }

    public function test_collection_taxonomy_properties_have_correct_names()
    {
      $json = Storage::get("taxonomy_properties_test_data.json");
      $tp = TaxonomyPropertyCollection::createFromAPI($json);
      $this->assertEquals($tp->getAt(0)->name, "Primary color");
      $this->assertEquals($tp->getAt(1)->name, "Secondary color");
    }

    public function test_collection_taxonomy_properties_have_correct_number_of_possible_values()
    {
      $json = Storage::get("taxonomy_properties_test_data.json");
      $tp = TaxonomyPropertyCollection::createFromAPI($json);
      $this->assertEquals(count($tp->getAt(0)->possible_values), 19);
      $this->assertEquals(count($tp->getAt(1)->possible_values), 19);
    }

    public function test_collection_taxonomy_properties_possible_values_are_of_correct_type()
    {
      $json = Storage::get("taxonomy_properties_test_data.json");
      $tp = TaxonomyPropertyCollection::createFromAPI($json);
      $this->assertEquals(get_class($tp->getAt(0)->possible_values[0]), TaxonomyPropertyPossibleValue::class);
    }

    public function test_collection_taxonomy_properties_possible_values_have_correct_names()
    {
      $json = Storage::get("taxonomy_properties_test_data.json");
      $tp = TaxonomyPropertyCollection::createFromAPI($json);
      $this->assertEquals($tp->getAt(0)->possible_values[0]->name, "Beige");
      $this->assertEquals($tp->getAt(0)->possible_values[1]->name, "Black");
    }

}
