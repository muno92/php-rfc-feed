<?php

namespace App\Tests\RfcFetcher;

use App\RfcFetcher\Entity\RfcDetail;
use App\RfcFetcher\RfcDetailExtractor;
use Generator;
use PHPUnit\Framework\TestCase;

class RfcDetailExtractorTest extends TestCase
{
    private RfcDetailExtractor $extractor;

    protected function setUp(): void
    {
        $this->extractor = new RfcDetailExtractor();
        parent::setUp();
    }

    /**
     * @dataProvider extractProvider
     */
    public function testExtract(string $filename, RfcDetail $expected): void
    {
        $html = file_get_contents( 'https://wiki.php.net/rfc/' . $filename);
        $detail = $this->extractor->extract($html);

        $this->assertEquals($expected, $detail);
    }

    public static function extractProvider(): Generator
    {
        yield 'Implemented' => [
            'attributes-on-constants',
            new RfcDetail(
                'Attributes on Constants',
                'Implemented',
                new \DateTimeImmutable('2025/04/29 19:52'),
                '0.2'
            )
        ];
        yield 'Declined' => [
            'short-and-inner-classes',
            new RfcDetail(
                'Nested Classes',
                'Declined',
                new \DateTimeImmutable('2025/05/18 12:53'),
                '0.5'
            )
        ];
        yield 'Withdrawn' => [
            'array-sort-return-array',
            new RfcDetail(
                'Change behaviour of array sort functions to return a copy of the sorted array',
                'Withdrawn',
                new \DateTimeImmutable('2025/04/03 13:08'),
                '0.1'
            )
        ];
        yield 'Obsolete' => [
            'property_write_visibility',
            new RfcDetail(
                'Property write/set visibility',
                'Obsolete',
                new \DateTimeImmutable('2025/04/03 13:08'),
                '0.4.6'
            )
        ];
        yield 'phpvcs' => [
            'phpvcs',
            new RfcDetail(
                "Move PHP's source code and docs to something that isn't CVS",
                'Accepted',
                new \DateTimeImmutable('2025/04/03 13:08'),
                '0.0.1'
            )
        ];
    }
}
