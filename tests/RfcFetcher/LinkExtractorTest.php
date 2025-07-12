<?php

namespace App\Tests\RfcFetcher;

use App\RfcFetcher\Entity\Link;
use App\RfcFetcher\LinkExtractor;
use PHPUnit\Framework\TestCase;

class LinkExtractorTest extends TestCase
{
    private LinkExtractor $extractor;

    protected function setUp(): void
    {
        $this->extractor = new LinkExtractor();

        parent::setUp();
    }


    public function testExtract(): void
    {
        $rfcList = $this->extractor->extract(file_get_contents('https://wiki.php.net/rfc'), 'https://wiki.php.net');

        $expectedLinks = [
            // Accepted (Process and Policy)
            new Link('Consolidate Coding Standards Policy Document', 'https://wiki.php.net/rfc/consolidate-coding-standard-policy-document'),
            // Attributes on Constants
            new Link('Attributes on Constants', 'https://wiki.php.net/rfc/attributes-on-constants'),
            // Implemented (PHP5.3)
            new Link('Closures', 'https://wiki.php.net/rfc/closures'),
            // Declined
            new Link('Nested Classes', 'https://wiki.php.net/rfc/short-and-inner-classes'),
            // Withdrawn
            new Link('Change behaviour of array sort functions to return a copy of the sorted array', 'https://wiki.php.net/rfc/array-sort-return-array'),
            // Inactive
            new Link('Clone with', 'https://wiki.php.net/rfc/clone_with'),
            // Obsolete
            new Link('Property write/set visibility', 'https://wiki.php.net/rfc/property_write_visibility'),
        ];

        $missingLinks = array_filter($expectedLinks, fn ($link) => !in_array($link, $rfcList));
        $this->assertEmpty($missingLinks, 'Expected links not found in RFC list: ' . implode(', ', array_map(fn (Link $link) => $link->title, $missingLinks)));
    }

    public function testExcludeSpecifiedRfc(): void
    {
        $rfcList = $this->extractor->extract(file_get_contents('https://wiki.php.net/rfc'), 'https://wiki.php.net');

        $extractedUrls = array_map(fn (Link $link) => $link->url, $rfcList);

        $this->assertFalse(array_all(LinkExtractor::EXCLUDE_RFCS, function ($rfc) use ($extractedUrls) {
            return in_array("https://wiki.php.net/rfc/{$rfc}", $extractedUrls, true);
        }), 'Excluded RFCs were found in the extracted links.');
    }

    public function testExcludeNonRfcLinks(): void
    {
        $rfcList = $this->extractor->extract(file_get_contents('https://wiki.php.net/rfc'), 'https://wiki.php.net');

        $this->assertTrue(array_all($rfcList, fn (Link $link) => str_starts_with($link->url, 'https://wiki.php.net/rfc/')), 'Not all links are valid RFC links.');
    }
}
