<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\GearBubble\Utils\PageScraper;
use App\GearBubble\Models\Campaign;

class PageScraperTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_a_page_scraper_returns_a_campaign()
    {
        $ps = new PageScraper("https://www.gearbubble.com/15homesteadmath");
        $ps->scrape();
        $c = $ps->getCampaign();
        $this->assertTrue(get_class($c) == Campaign::class);
    }

    public function test_a_page_scraper_returns_a_campaign_with_correct_title()
    {
        $ps = new PageScraper("https://www.gearbubble.com/15homesteadmath");
        $ps->scrape();
        $c = $ps->getCampaign();
        $this->assertEquals($c->title, "15 oz Homestead Math");
    }


}
