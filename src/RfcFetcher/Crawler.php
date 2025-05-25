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

        $converter = function (DomCrawler $node) {
            $title = $node->text();
            $url = 'https://wiki.php.net' . $node->attr('href');

            return new Link($title, $url);
        };
        $votingRfcs = $crawler->filter('#in_voting_phase + div a')->each($converter);
        $underDiscussionRfcs = $crawler->filter('#under_discussion + div a')->each($converter);
        $draftRfcs = $crawler->filter('#in_draft + div a')->each($converter);
        $processAndPolicyRfcs = $crawler->filter('#process_and_policy + div a')->each($converter);

        return [...$votingRfcs, ...$underDiscussionRfcs, ...$draftRfcs, ...$processAndPolicyRfcs];
    }
}
