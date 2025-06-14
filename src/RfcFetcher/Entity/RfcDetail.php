<?php

namespace App\RfcFetcher\Entity;

final readonly class RfcDetail
{
    public function __construct(
        public string $title,
        public string $status,
        public \DateTimeImmutable $lastUpdated,
        public string $version
    ) {}
}
