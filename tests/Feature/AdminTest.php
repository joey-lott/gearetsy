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

class AdminTest extends TestCase
{

    public function test_an_admin_dashboard_exists()
    {
      $response = $this->get("/admin");

      // The status is 302 if non-admin user because redirects.
      // The main thing asserted here is that the status is NOT 404 (ie. it exists)
      $response->assertStatus(302);
    }

    public function test_an_admin_dashboard_redirects_nonusers()
    {
      $response = $this->get("/admin");
      $response->assertRedirect("/");
    }

    public function test_an_admin_dashboard_allows_admin_users()
    {
      $this->setUpAdminUser();

      $response = $this->get("/admin");
      $response->assertStatus(200);
    }

    public function test_an_admin_dashboard_redirects_nonadmin_users()
    {
      // First, create a nonadmin user
      $user = factory(User::class)->create();

      // Next, act as that admin user
      $this->actingAs($user);

      $response = $this->get("/admin");
      $response->assertStatus(302);
    }

    public function test_an_admin_dashboard_has_an_option_to_view_analytics()
    {
      $this->setUpAdminUser();

      $response = $this->get("/admin");
      $response->assertSee("view api analytics");
    }

    public function test_an_admin_api_analytics_dashboard_exists()
    {
      $response = $this->get("/admin/api-analytics");

      // The status is 302 if non-admin user because redirects.
      // The main thing asserted here is that the status is NOT 404 (ie. it exists)
      $response->assertStatus(302);
    }

    public function test_an_admin_api_analytics_dashboard_allows_admin_users()
    {
      $this->setUpAdminUser();

      $response = $this->get("/admin/api-analytics");
      $response->assertStatus(200);
    }

    public function test_an_admin_dashboard_has_an_option_to_authorize_a_new_user()
    {
      $this->setUpAdminUser();

      $response = $this->get("/admin");
      $response->assertSee("authorize a new user");
    }

    public function test_an_admin_provisional_user_form_dashboard_exists()
    {
      $response = $this->get("/admin/add-provisional");

      // The status is 302 if non-admin user because redirects.
      // The main thing asserted here is that the status is NOT 404 (ie. it exists)
      $response->assertStatus(302);
    }

    public function test_an_admin_provisional_user_form_allows_admin_users()
    {
      $this->setUpAdminUser();

      $response = $this->get("/admin/add-provisional");
      $response->assertStatus(200);
    }

    private function setUpAdminUser() {
      // First, create an admin user
      $admin = factory(Admin::class)->create();
      $user = User::all()->first();

      // Next, act as that admin user
      $this->actingAs($user);
    }

}
