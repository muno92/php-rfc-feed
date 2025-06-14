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

        $title = $crawler->filter('.sectionedit1')->text();
        $title = str_replace('PHP RFC: ', '', $title);
        $title = str_replace('Request for Comments: ', '', $title);

        $status = '';
        $statusNodes = $crawler->filter('.sectionedit1 + div > ul > li')
            ->reduce(function (Crawler $node) {
                return str_contains($node->text(), 'Status:');
            });

        if ($statusNodes->count() > 0) {
            $text = $statusNodes->first()->text();
            preg_match('/Status:\s*(.+)/', $text, $matches);
            $status = $matches[1] ?? '';
        }

        $version = '';
        $versionNodes = $crawler->filter('.sectionedit1 + div > ul > li')
            ->reduce(function (Crawler $node) {
                return str_contains($node->text(), 'Version:');
            });

        if ($versionNodes->count() > 0) {
            $text = $versionNodes->first()->text();
            preg_match('/Version:\s*(.+)/', $text, $matches);
            $version = $matches[1] ?? '';
        }

        $rfcInfo = $crawler->filter('div.docInfo')->text();
        preg_match('/Last modified: (\d{4}\/\d{2}\/\d{2} \d{2}:\d{2})/', $rfcInfo, $matches);

        $lastUpdated = new \DateTimeImmutable($matches[1] ?? 'now');

        return new RfcDetail($title, $status, $lastUpdated, $version);
    }
}
