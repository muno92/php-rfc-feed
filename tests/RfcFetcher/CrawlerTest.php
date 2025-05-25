<?php

namespace App\Tests\RfcFetcher;

use App\RfcFetcher\Crawler;
use App\RfcFetcher\Entity\Link;
use Generator;
use PHPUnit\Framework\TestCase;

class CrawlerTest extends TestCase
{
    /**
     * @dataProvider crawlProvider
     */
    public function testCrawl(Link $expected): void
    {
        $crawler = new Crawler();
        $rfcList = $crawler->crawl(file_get_contents(__DIR__ . '/../fixtures/rfc.html'));

        $this->assertContainsEquals($expected, $rfcList);
    }

    public static function crawlProvider(): Generator
    {
        yield 'In voting phase' => [
            new Link('Pipe operator', 'https://wiki.php.net/rfc/pipe-operator-v3'),
        ];
    }
}
