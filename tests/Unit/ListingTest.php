<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Etsy\Models\Listing;
use App\Etsy\Models\PropertyValue;
use App\Etsy\Models\ListingCollection;
use App\Etsy\Models\ListingInventory;
use App\Etsy\Models\ListingOffering;
use App\Etsy\Models\ListingProduct;
use App\Etsy\Models\ListingStagingData;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;

class ListingTest extends TestCase
{

    public function test_can_create_listing_with_correct_values()
    {
      $urls = ["http://a", "http://b"];
      $l = new Listing("Title", "Description", 1.01, 12345, "tag,tag,tag", 54321, $urls);
      $this->assertEquals($l->title, "Title");
      $this->assertEquals($l->description, "Description");
      $this->assertEquals($l->price, 1.01);
      $this->assertEquals($l->taxonomy_id, 12345);
      $this->assertEquals($l->tags, "tag,tag,tag");
      $this->assertEquals($l->shipping_template_id, 54321);
      $this->assertEquals($l->imagesToAddFromUrl, $urls);
    }

    public function test_can_add_image_url_and_get_correct_count()
    {
      $urls = ["http://a", "http://b"];
      $l = new Listing("Title", "Description", 1.01, 12345, "tag,tag,tag", 54321, $urls);
      $this->assertEquals(count($l->imagesToAddFromUrl), 2);
      $l->addImageUrl("http://c");
      $this->assertEquals(count($l->imagesToAddFromUrl), 3);
    }

    public function test_listing_returns_image_urls_in_reverse_order()
    {
      $urls = ["http://a", "http://b"];
      $l = new Listing("Title", "Description", 1.01, 12345, "tag,tag,tag", 54321, $urls);
      $l->addImageUrl("http://c");
      $reversedUrls = ["http://c", "http://b","http://a"];
      $this->assertEquals($l->imagesToAddFromUrlReversed(), $reversedUrls);
    }

    public function test_can_create_listing_inventory_with_correct_values()
    {
      $l = new ListingInventory(12345);
      $this->assertEquals($l->listing_id, 12345);
    }

    public function test_can_add_product_to_listing_inventory()
    {
      $l = new ListingInventory(12345);
      $p = new ListingProduct();
      $l->addProduct($p);
      $this->assertEquals(count($l->products), 1);
      $p = new ListingProduct();
      $l->addProduct($p);
      $this->assertEquals(count($l->products), 2);
    }

    public function test_can_add_product_to_listing_staging_data()
    {
      $l = new ListingStagingData();
      $p = new ListingProduct();
      $l->addProduct($p);
      $this->assertEquals(count($l->products), 1);
      $p = new ListingProduct();
      $l->addProduct($p);
      $this->assertEquals(count($l->products), 2);
    }

    public function test_listing_can_generate_listing_inventory_from_staging()
    {
      // Create Listing
      $urls = ["http://a", "http://b"];
      $l = new Listing("Title", "Description", 1.01, 12345, "tag,tag,tag", 54321, $urls);

      // Create staging
      $ls = new ListingStagingData();
      $p = new ListingProduct();
      $ls->addProduct($p);

      // Assign staging to listing
      $l->staging = $ls;

      // Get the listing inventory based on the staging data, given the listng ID.
      // The use case is that the listing inventory needs to be generated after
      // the listing ID is returned following adding the listing to Etsy.
      $li = $l->createListingInventory(12345);

      $this->assertEquals(get_class($li), ListingInventory::class);
    }

    public function test_property_value_can_correctly_json_encode_itself()
    {
      $pv = new PropertyValue(1,2, ["red", "orange"]);
      $expected = '{"property_id":1,"scale_id":2,"values":["red","orange"]}';
      $this->assertEquals(json_encode($pv), $expected);
      $pv = new PropertyValue(1,2, null, [3, 4]);
      $expected = '{"property_id":1,"scale_id":2,"value_ids":[3,4]}';
      $this->assertEquals(json_encode($pv), $expected);
    }

    public function test_listing_offering_can_correctly_json_encode_itself()
    {
      $lo = new ListingOffering(1.01);
      $expected = '{"price":1.01,"quantity":999,"is_enabled":1}';
      $this->assertEquals(json_encode($lo), $expected);
    }

    public function test_listing_product_can_correctly_json_encode_itself()
    {
      $pv = new PropertyValue(1,2, ["red", "orange"]);
      $pv2 = new PropertyValue(1,2, null, [3, 4]);
      $lo = new ListingOffering(1.01);
      $lp = new ListingProduct([$pv, $pv2], [$lo]);
      $expectedPv = '{"property_id":1,"scale_id":2,"values":["red","orange"]}';
      $expectedPv2 = '{"property_id":1,"scale_id":2,"value_ids":[3,4]}';
      $expectedLo = '{"price":1.01,"quantity":999,"is_enabled":1}';
      $expected = '{"property_values":['.$expectedPv.','.$expectedPv2.'],"sku":"","offerings":['.$expectedLo.']}';
      $this->assertEquals(json_encode($lp), $expected);
    }

}
