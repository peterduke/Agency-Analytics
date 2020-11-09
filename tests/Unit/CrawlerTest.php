<?php

namespace Tests\Unit;

use App\Services\Crawler;
use Tests\TestCase;
use InvalidArgumentException;

class CrawlerTest extends TestCase
{

    /**
     * Need to initialize Crawler with a valid URL containing scheme and domain.
     */
    public function testConstructorBadArgument()
    {
        $this->expectException(InvalidArgumentException::class);
        $c = new Crawler('foobar');
    }

    /**
     * test will fail by exception being thrown
     */
    public function testConstructorGoodArgument()
    {
        $c = new Crawler('https://agencyanalytics.com');
        $this->assertInstanceOf(Crawler::class, $c);
    }

    public function testRun()
    {
        $c = new Crawler('https://agencyanalytics.com');
        $c->run();
        var_dump($c->report());
    }

    public function testIsLinkInternal()
    { 
        $c = new Crawler('https://agencyanalytics.com');
        
        $this->assertTrue($c->isLinkInternal('/'));
        $this->assertTrue($c->isLinkInternal('/foo.bar'));
        $this->assertTrue($c->isLinkInternal('agencyanalytics.com'));
        $this->assertTrue($c->isLinkInternal('agencyanalytics.com/foo.bar'));
        $this->assertTrue($c->isLinkInternal('//agencyanalytics.com'));
        $this->assertTrue($c->isLinkInternal('//agencyanalytics.com/foo.bar'));
        $this->assertTrue($c->isLinkInternal('http://agencyanalytics.com'));
        $this->assertTrue($c->isLinkInternal('http://agencyanalytics.com/foo.bar'));
        $this->assertTrue($c->isLinkInternal('https://agencyanalytics.com'));
        $this->assertTrue($c->isLinkInternal('https://agencyanalytics.com/foo.bar'));

        $this->assertFalse($c->isLinkInternal('google.com'));
        $this->assertFalse($c->isLinkInternal('//google.com'));
        $this->assertFalse($c->isLinkInternal('http://google.com'));
        $this->assertFalse($c->isLinkInternal('https://google.com'));
    }
}
