<?php

namespace App\RfcFetcher;

use App\RfcFetcher\Entity\Link;

final class Crawler
{
    /**
     * @param string $html
     * @return list<Link>
     */
    public function crawl(string $html): array
    {
        return [];
    }
}
