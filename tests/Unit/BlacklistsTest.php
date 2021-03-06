<?php

namespace Tests\Unit;

use App\Exceptions\AdvertiserNotFoundException;
use App\Exceptions\InvalidBlacklistsFormatException;
use App\Exceptions\PublisherNotFoundException;
use App\Exceptions\SiteNotFoundException;
use App\Models\Advertisers;
use App\Models\Blacklist;
use App\Models\Publisher;
use App\Models\Site;
use App\Services\Blacklists;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class BlacklistsTest extends TestCase
{
    use DatabaseMigrations;


    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        Site::insert([
            ['name' => 'site1'],
            ['name' => 'site2'],
            ['name' => 'site3'],
        ]);
        Advertisers::insert([
            ['name' => 'advertiser1'],
            ['name' => 'advertiser2']
        ]);
        Publisher::insert([
            ['name' => 'publisher1'],
            ['name' => 'publisher2']
        ]);
        Blacklist::insert([
            ['advertiser_id' => 1, 'value' => 1, 'type' => 's'],
            ['advertiser_id' => 1, 'value' => 1, 'type' => 'p'],
            ['advertiser_id' => 1, 'value' => 3, 'type' => 's'],
            ['advertiser_id' => 1, 'value' => 2, 'type' => 'p'],
        ]);
    }


    public function testSuccessfulSave()
    {
        Blacklists::save('s1, p1, s2, s3, p2', 2);

        $blacklists = [
            ['advertiser_id' => 2, 'value' => 1, 'type' => 's'],
            ['advertiser_id' => 2, 'value' => 1, 'type' => 'p'],
            ['advertiser_id' => 2, 'value' => 2, 'type' => 's'],
            ['advertiser_id' => 2, 'value' => 3, 'type' => 's'],
            ['advertiser_id' => 2, 'value' => 2, 'type' => 'p'],
        ];

        foreach ($blacklists as $blacklist) {
            $this->assertDatabaseHas('blacklists', $blacklist);
        }
    }

    public function testNotFoundSitesSave()
    {
        $this->expectException(SiteNotFoundException::class);

        Blacklists::save('s1, p1, s5, s3, p2', 1);
    }

    public function testNotFoundPublishersSave()
    {
        $this->expectException(PublisherNotFoundException::class);

        Blacklists::save('s1, p1, s2, s3, p20', 1);
    }

    public function testNotFoundAdvertiserSave()
    {
        $this->expectException(AdvertiserNotFoundException::class);

        Blacklists::save('s1, p1, s2, s3, p2', 25);
    }

    public function testSuccessfulGet()
    {
        $blacklists = Blacklists::get(1);

        $this->assertEquals('p1, p2, s1, s3', $blacklists);
    }

    public function testEmptyBlacklistsGet()
    {
        $blacklists = Blacklists::get(31);

        $this->assertEquals('', $blacklists);
    }

    public function testInvalidFormatSave()
    {
        $this->expectException(InvalidBlacklistsFormatException::class);

        Blacklists::save('s1, p1.s2s3, p2', 1);
    }

    public function testInvalidFormatTypeSave()
    {
        $this->expectException(InvalidBlacklistsFormatException::class);

        Blacklists::save('ss1, p1, s2, s3, p2', 1);
    }

    public function testInvalidFormatValueSave()
    {
        $this->expectException(InvalidBlacklistsFormatException::class);

        Blacklists::save('s1, p1, s2gh, s3, p2', 1);
    }

    public function testInvalidFormatValueAboveZeroSave()
    {
        $this->expectException(InvalidBlacklistsFormatException::class);

        Blacklists::save('s1, p1, s2, s-3, p2', 1);
    }
}
