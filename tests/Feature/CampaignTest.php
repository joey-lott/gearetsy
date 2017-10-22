<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\GearBubble\Utils\PageScraper;
use App\GearBubble\Models\Campaign;
use App\Etsy\Models\ListingCollection;

class CampaignTest extends TestCase
{

    private $c;

    private function setUpCampaignFromPageScraper($url = "https://www.gearbubble.com/15homesteadmath") {
      $ps = new PageScraper($url);
      $ps->scrape();
      $this->c = $ps->getCampaign();
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

    public function test_a_campaign_can_produce_a_listing_collection_with_four_listings()
    {
      // This URL should convert to four listings
      $this->setUpCampaignFromPageScraper("https://www.gearbubble.com/testbud");
      $lc = $this->c->getListingCollection();
      $this->assertEquals($lc->count(), 4);
    }

    public function test_a_campaign_can_produce_a_listing_collection_with_four_listings_and_each_listing_should_have_a_price()
    {
      // This URL should convert to four listings
      $this->setUpCampaignFromPageScraper("https://www.gearbubble.com/testbud");
      $lc = $this->c->getListingCollection();
      $this->assertGreaterThan(1, $lc->getAt(0)->price);
      $this->assertGreaterThan(1, $lc->getAt(1)->price);
      $this->assertGreaterThan(1, $lc->getAt(2)->price);
      $this->assertGreaterThan(1, $lc->getAt(3)->price);
    }

}
