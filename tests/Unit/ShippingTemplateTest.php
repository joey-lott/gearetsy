<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Etsy\Models\ShippingTemplate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;

class ShippingTemplateTest extends TestCase
{

    public function test_can_create_shipping_template_collection_from_json()
    {
      $json = Storage::get("one_shipping_template_test_data.json");
      $st = ShippingTemplate::createFromAPI($json);
      $this->assertEquals(get_class($st), Collection::class);
    }


    public function test_collection_has_correct_number_of_templates()
    {
      $json = Storage::get("one_shipping_template_test_data.json");
      $st = ShippingTemplate::createFromAPI($json);
      $this->assertEquals($st->count(), 1);
    }

    public function test_collection_has_correct_number_of_templates_multiple_templates()
    {
      $json = Storage::get("three_shipping_template_test_data.json");
      $st = ShippingTemplate::createFromAPI($json);
      $this->assertEquals($st->count(), 3);
    }

    public function test_collection_items_are_of_correct_type()
    {
      $json = Storage::get("three_shipping_template_test_data.json");
      $st = ShippingTemplate::createFromAPI($json);
      foreach($st as $t) {
        $this->assertEquals(get_class($t), ShippingTemplate::class);
      }
    }

    public function test_collection_items_have_expected_title()
    {
      $json = Storage::get("three_shipping_template_test_data.json");
      $st = ShippingTemplate::createFromAPI($json);
      $i = 1;
      foreach($st as $t) {
        $this->assertEquals($t->title, "Title ".$i);
        $i++;
      }
    }

    public function test_collection_items_have_expected_template_id()
    {
      $json = Storage::get("three_shipping_template_test_data.json");
      $st = ShippingTemplate::createFromAPI($json);
      $i = 1;
      foreach($st as $t) {
        $this->assertEquals($t->shipping_template_id, $i);
        $i++;
      }
    }
}
