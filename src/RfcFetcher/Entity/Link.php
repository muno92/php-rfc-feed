<?php

namespace App\RfcFetcher\Entity;

final readonly class Link
{
    public function __construct(public string $title, public string $url)
    {}
}
