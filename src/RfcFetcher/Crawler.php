<?php

namespace App\RfcFetcher;

use App\RfcFetcher\Entity\Link;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

final class Crawler
{
    /**
     * @param string $html
     * @return list<Link>
     */
    public function crawl(string $html): array
    {
        $crawler = new DomCrawler($html);

        $votings = $crawler->filter('#in_voting_phase + div a')->each(function (DomCrawler $node) {
            $title = $node->text();
            $url = 'https://wiki.php.net' . $node->attr('href');

            return new Link($title, $url);
        });

        return $votings;
    }
}
