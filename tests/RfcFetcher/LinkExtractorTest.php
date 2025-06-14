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
        $rfcList = $crawler->extract(file_get_contents(__DIR__ . '/../fixtures/rfc.html'), 'https://wiki.php.net');

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
        yield 'Implemented (PHP8.5)' => [
            new Link('Attributes on Constants', 'https://wiki.php.net/rfc/attributes-on-constants'),
        ];
        yield 'Implemented (PHP5.3)' => [
            new Link('Closures', 'https://wiki.php.net/rfc/closures'),
        ];
        yield 'Declined' => [
            new Link('Nested Classes', 'https://wiki.php.net/rfc/short-and-inner-classes'),
        ];
        yield 'Withdrawn' => [
            new Link('Change behaviour of array sort functions to return a copy of the sorted array', 'https://wiki.php.net/rfc/array-sort-return-array'),
        ];
        yield 'Inactive' => [
            new Link('Clone with', 'https://wiki.php.net/rfc/clone_with'),
        ];
        yield 'Obsolete' => [
            new Link('Property write/set visibility', 'https://wiki.php.net/rfc/property_write_visibility'),
        ];
    }
}
