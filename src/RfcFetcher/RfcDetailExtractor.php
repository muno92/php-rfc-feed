<?php

namespace App\RfcFetcher;

use App\RfcFetcher\Entity\RfcDetail;
use Symfony\Component\DomCrawler\Crawler;

final class RfcDetailExtractor
{
    /**
     * Extract RFC details from HTML
     */
    public function extract(string $html): RfcDetail
    {
        $crawler = new Crawler($html);

        $title = str_replace('PHP RFC: ', '', $crawler->filter('h1.sectionedit1')->text());

        $status = '';
        $statusNodes = $crawler->filter('h1.sectionedit1 + div.level1 > ul > li')
            ->reduce(function (Crawler $node) {
                return str_contains($node->text(), 'Status:');
            });

        if ($statusNodes->count() > 0) {
            $text = $statusNodes->first()->text();
            preg_match('/Status:\s*(.+)/', $text, $matches);
            $status = $matches[1] ?? '';
        }

        $rfcInfo = $crawler->filter('div.docInfo')->text();
        preg_match('/Last modified: (\d{4}\/\d{2}\/\d{2} \d{2}:\d{2})/', $rfcInfo, $matches);

        $lastUpdated = new \DateTimeImmutable($matches[1] ?? 'now');

        return new RfcDetail($title, $status, $lastUpdated);
    }
}
