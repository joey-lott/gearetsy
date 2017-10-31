<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\Admin;

class AdminTest extends TestCase
{
  use RefreshDatabase;

    public function test_a_user_can_correctly_report_whether_it_is_an_admin()
    {
      // First, create an admin user
      $admin = factory(Admin::class)->create();
      $user = User::all()->first();
      $this->assertTrue($user->isAdmin());

      // Second, create a non-admin user
      $user = factory(User::class)->create();
      $this->assertFalse($user->isAdmin());
    }
}
