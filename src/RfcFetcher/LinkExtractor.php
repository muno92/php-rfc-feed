<?php

namespace App\RfcFetcher;

use App\RfcFetcher\Entity\Link;
use Symfony\Component\DomCrawler\Crawler;

final class LinkExtractor
{
    /**
     * @param string $html
     * @return list<Link>
     */
    public function extract(string $html, string $host): array
    {
        $crawler = new Crawler($html);

        $converter = function (Crawler $node) use ($host): Link {
            $title = $node->text();
            $url = $host . $node->attr('href');

            return new Link($title, $url);
        };
        $votingRfcs = $crawler->filter('#in_voting_phase + div a')->each($converter);
        $underDiscussionRfcs = $crawler->filter('#under_discussion + div a')->each($converter);
        $draftRfcs = $crawler->filter('#in_draft + div a')->each($converter);
        $processAndPolicyRfcs = $crawler->filter('#process_and_policy + div a')->each($converter);
        $pendingImplementationLanding = $crawler->filter('#pending_implementationlanding + div a')->each($converter);

        return [
            ...$votingRfcs,
            ...$underDiscussionRfcs,
            ...$draftRfcs,
            ...$processAndPolicyRfcs,
            ...$pendingImplementationLanding,
        ];
    }
}
