<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Etsy\Models\ShippingTemplate;
use App\User;
use Illuminate\Support\Facades\Storage;


class ShippingTemplatesTest extends TestCase
{
//  use RefreshDatabase;

    public function test_shipping_templates_are_cached_in_json_files_for_each_user()
    {
      $user = new User();
      $user->oauthToken = "4fec3a042f3d497b3f8a45f7f6e02e";
      $user->oauthTokenSecret = "ab0434be15";
      $this->actingAs($user);

      // Get all the templates for a valid user (using Joey's Etsy user ID)
      ShippingTemplate::getAllShippingTemplatesForUser("92513508");

      $exists = Storage::exists("shipping_templates_92513508.json");

      $this->assertTrue($exists);
    }

}
