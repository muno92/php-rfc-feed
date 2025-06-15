<?php

namespace App\Tests\RfcFetcher\TestData;

final readonly class RfcDetailTestData
{
    public function __construct(
        public string $filename,
        public \App\RfcFetcher\Entity\RfcDetail $rfcDetail
    ) {
    }
}
