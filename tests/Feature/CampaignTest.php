<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\GearBubble\Utils\PageScraper;
use App\GearBubble\Models\Campaign;
use App\GearBubble\Models\ListingFormGroupCollection;
use App\Etsy\Models\ListingCollection;
use App\User;

class CampaignTest extends TestCase
{

    private $c;

    private function setUpCampaignFromPageScraper($url = "https://www.gearbubble.com/15homesteadmath") {
      $ps = new PageScraper($url);
      $ps->scrape();
      $this->c = $ps->getCampaign();

      $user = new User();
      $user->oauthToken = "4fec3a042f3d497b3f8a45f7f6e02e";
      $user->oauthTokenSecret = "ab0434be15";
//      dump($user);
      $this->actingAs($user);
    }

    public function test_a_campaign_can_produce_a_listing_collection()
    {
      $this->setUpCampaignFromPageScraper();
      $lc = $this->c->getListingCollection();
      $this->assertTrue(get_class($lc) == ListingCollection::class);
    }

    public function test_a_campaign_can_produce_a_listing_collection_with_at_least_one_listing()
    {
      $this->setUpCampaignFromPageScraper();
      $lc = $this->c->getListingCollection();
      $this->assertTrue($lc->count() > 0);
    }

    public function test_a_campaign_can_produce_a_listing_collection_with_at_least_one_listing_with_the_correct_title()
    {
      $this->setUpCampaignFromPageScraper();
      $lc = $this->c->getListingCollection();
      $this->assertEquals($lc->first()->title, "15 oz Homestead Math");
    }

    public function test_a_campaign_can_produce_a_listing_collection_with_five_listings()
    {
      // This URL should convert to five listings
      $this->setUpCampaignFromPageScraper("https://www.gearbubble.com/testbud");
      $lc = $this->c->getListingCollection();
      $this->assertEquals($lc->count(), 5);
    }

    public function test_a_campaign_can_produce_a_listing_collection_with_five_listings_and_each_listing_should_have_a_price()
    {
      // This URL should convert to five listings
      $this->setUpCampaignFromPageScraper("https://www.gearbubble.com/testbud");
      $lc = $this->c->getListingCollection();
      $this->assertGreaterThan(1, $lc->getAt(0)->price);
      $this->assertGreaterThan(1, $lc->getAt(1)->price);
      $this->assertGreaterThan(1, $lc->getAt(2)->price);
      $this->assertGreaterThan(1, $lc->getAt(3)->price);
      $this->assertGreaterThan(1, $lc->getAt(4)->price);
    }

    public function test_a_campaign_can_produce_a_listing_form_group_collection()
    {
      // This URL should convert to five listings
      $this->setUpCampaignFromPageScraper("https://www.gearbubble.com/testbud");
      $lfgc = $this->c->getFormFieldCollection();
      $this->assertEquals(get_class($lfgc), ListingFormGroupCollection::class);
    }

    public function test_a_campaign_can_produce_a_listing_form_group_collection_with_correct_number_of_items()
    {
      // This URL should convert to five listings
      $this->setUpCampaignFromPageScraper("https://www.gearbubble.com/testbud");
      $lfgc = $this->c->getFormFieldCollection();
      $this->assertEquals($lfgc->count(), 5);
    }

    public function test_a_campaign_can_produce_a_listing_form_group_collection_and_that_each_form_group_has_the_correct_title()
    {
      // This URL should convert to five listings
      $this->setUpCampaignFromPageScraper("https://www.gearbubble.com/testbud");
      $lfgc = $this->c->getFormFieldCollection();
      $lfg = $lfgc->getAt(0);
      $this->assertEquals($lfg->title->value, "Test Bud");
    }

    public function test_a_campaign_can_produce_a_listing_form_group_collection_and_that_each_form_group_has_the_correct_price()
    {
      // This URL should convert to five listings
      $this->setUpCampaignFromPageScraper("https://www.gearbubble.com/testbud");
      $lfgc = $this->c->getFormFieldCollection();
      $lfg = $lfgc->getAt(0);
      $this->assertEquals($lfg->primaryVariations[0]->value, "22.5");
    }

}
