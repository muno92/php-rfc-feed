<?php

namespace App\Tests\RfcFetcher;

use App\RfcFetcher\LinkExtractor;
use App\RfcFetcher\Entity\Link;
use Generator;
use PHPUnit\Framework\TestCase;

class LinkExtractorTest extends TestCase
{
    /**
     * @dataProvider extractProvider
     */
    public function testExtract(Link $expected): void
    {
        $crawler = new LinkExtractor();
        $rfcList = $crawler->extract(file_get_contents(__DIR__ . '/../fixtures/rfc.html'));

        $this->assertContainsEquals($expected, $rfcList);
    }

    public static function extractProvider(): Generator
    {
        yield 'In voting phase' => [
            new Link('Pipe operator', 'https://wiki.php.net/rfc/pipe-operator-v3'),
        ];
        yield 'Under discussion' => [
            new Link('Clone with v2', 'https://wiki.php.net/rfc/clone_with_v2'),
        ];
        yield 'In Draft' => [
            new Link('Make OPcache a non-optional part of PHP', 'https://wiki.php.net/rfc/make_opcache_required'),
        ];
        yield 'In Draft (nested)' => [
            new Link("Pattern matching ''is'' keyword", 'https://wiki.php.net/rfc/pattern-matching'),
        ];
        yield 'Accepted (Process and Policy)' => [
            new Link('Consolidate Coding Standards Policy Document', 'https://wiki.php.net/rfc/consolidate-coding-standard-policy-document'),
        ];
        yield 'Accepted (Pending Implementation / Landing)' => [
            new Link('Deprecations for PHP 8.4', 'https://wiki.php.net/rfc/deprecations_php_8_4'),
        ];
    }
}
