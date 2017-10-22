<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Etsy\Models\Listing;
use App\Etsy\Models\ListingCollection;

class ListingCollectionTest extends TestCase
{

    public function test_it_can_add_a_listing()
    {
      $lc = new ListingCollection();
      $lc->add(new Listing("title", "description", 0.95, 12345, "", 12345, []));
      $this->assertEquals($lc->count(), 1);
    }

    public function test_it_can_add_multiple_listings() {
      $lc = new ListingCollection();
      $lc->add(new Listing("title", "description", 0.95, 12345, "", 12345, []));
      $lc->add(new Listing("title", "description", 0.95, 12345, "", 12345, []));
      $this->assertEquals($lc->count(), 2);
    }

}
