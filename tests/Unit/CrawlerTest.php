<?php

namespace Tests\Unit;

use App\Services\Crawler;
use Tests\TestCase;

class CrawlerTest extends TestCase
{

    public function testRun()
    {
        $c = new Crawler();
        $c->setInternalDomain('agencyanalytics.com');
        $c->addUrl('https://agencyanalytics.com');
        $c->run();
        var_dump($c);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testParse()
    {
        $html = file_get_contents(__DIR__ . '/../../docs/agencyanalytics.com.html');
        $c = new Crawler();
        $values = $c->parse($html);
    }

    public function testIsLinkInternal()
    {
        $c = new Crawler();
        $c->setInternalDomain('agencyanalytics.com');
        
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
