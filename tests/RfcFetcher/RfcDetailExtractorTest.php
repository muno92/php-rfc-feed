<?php

namespace App\Tests\RfcFetcher;

use App\RfcFetcher\RfcDetailExtractor;
use App\RfcFetcher\Entity\RfcDetail;
use PHPUnit\Framework\TestCase;
use Generator;

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
        $html = file_get_contents(__DIR__ . '/../fixtures/rfc_details/' . $filename);
        $detail = $this->extractor->extract($html);

        $this->assertEquals($expected, $detail);
    }

    public static function extractProvider(): Generator
    {
        yield 'num_available_processors' => [
            'num_available_processors.html',
            new RfcDetail(
                'num_available_processors',
                'Under Discussion',
                new \DateTimeImmutable('2025/05/24 17:36')
            )
        ];
        yield 'str_icontains' => [
            'str_icontains.html',
            new RfcDetail(
                'str_icontains',
                'In Draft',
                new \DateTimeImmutable('2025/06/13 08:39')
            )
        ];
        yield 'clone_with_v2' => [
            'clone_with_v2.html',
            new RfcDetail(
                'Clone with v2',
                'Voting',
                new \DateTimeImmutable('2025/06/04 15:08')
            )
        ];
        yield 'attributes-on-constants' => [
            'attributes-on-constants.html',
            new RfcDetail(
                'Attributes on Constants',
                'Implemented',
                new \DateTimeImmutable('2025/04/29 19:52')
            )
        ];
        yield 'deprecations_php_8_4' => [
            'deprecations_php_8_4.html',
            new RfcDetail(
                'Deprecations for PHP 8.4',
                'Pending Implementation',
                new \DateTimeImmutable('2025/04/03 13:08')
            )
        ];
        yield 'short-and-inner-classes' => [
            'short-and-inner-classes.html',
            new RfcDetail(
                'Nested Classes',
                'Declined',
                new \DateTimeImmutable('2025/05/18 12:53')
            )
        ];
        yield 'array-sort-return-array' => [
            'array-sort-return-array.html',
            new RfcDetail(
                'Change behaviour of array sort functions to return a copy of the sorted array',
                'Withdrawn',
                new \DateTimeImmutable('2025/04/03 13:08')
            )
        ];
        yield 'property_write_visibility' => [
            'property_write_visibility.html',
            new RfcDetail(
                'Property write/set visibility',
                'Obsolete',
                new \DateTimeImmutable('2025/04/03 13:08')
            )
        ];
        yield 'phpvcs' => [
            'phpvcs.html',
            new RfcDetail(
                "Move PHP's source code and docs to something that isn't CVS",
                'Accepted',
                new \DateTimeImmutable('2025/04/03 13:08')
            )
        ];
    }
}
