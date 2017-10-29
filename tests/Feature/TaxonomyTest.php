<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\ShippingTemplate;
use App\User;

class TaxonomyTest extends TestCase
{


    protected function setUp() {
      parent::setUp();
      $user = new User();
      $user->oauthToken = "4fec3a042f3d497b3f8a45f7f6e02e";
      $user->oauthTokenSecret = "ab0434be15";
//      dump($user);
      $this->actingAs($user);
    }

    public function test_a_taxonomy_property_collection()
    {
      ShippingTemplate::getAllShippingTemplatesForUser(92513508);
//      $this->assertTrue(get_class($lc) == ListingCollection::class);
    }

}
