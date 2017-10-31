<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\GearBubble\Utils\PageScraper;
use App\GearBubble\Models\Campaign;
use App\GearBubble\Models\ListingFormGroupCollection;
use App\Etsy\Models\ListingCollection;
use App\User;
use App\Admin;
use App\Description;

class DescriptionTest extends TestCase
{
  use RefreshDatabase;

    public function test_a_nonuser_is_redirected_from_description_index()
    {
      $response = $this->get("/description");

      // The status is 302 if non-admin user because redirects.
      // The main thing asserted here is that the status is NOT 404 (ie. it exists)
      $response->assertStatus(302);
    }

    public function test_a_user_can_access_description_index()
    {
      $user = factory(User::class)->create();
      $this->actingAs($user);
      $response = $this->get("/description");
      $response->assertStatus(200);
    }

    public function test_a_user_with_no_description_does_not_see_description_list_heading()
    {
      $user = factory(User::class)->create();
      $this->actingAs($user);
      $response = $this->get("/description");
      $response->assertDontSee("Select a description to edit it");
    }

    public function test_a_user_with_a_description_does_see_description_list_heading()
    {
      $description = factory(Description::class)->create();
      $user = User::all()->first();
      $this->actingAs($user);
      $response = $this->get("/description");
      $response->assertSee("Select a description to edit it");
    }

    public function test_a_user_with_a_description_does_see_description_title_listed()
    {
      $description = factory(Description::class)->create();
      $user = User::all()->first();
      $this->actingAs($user);
      $response = $this->get("/description");
      $response->assertSee($description->title);
    }

    private function setUpAdminUser() {
      // First, create an admin user
      $admin = factory(Admin::class)->create();
      $user = User::all()->first();

      // Next, act as that admin user
      $this->actingAs($user);
    }

}
