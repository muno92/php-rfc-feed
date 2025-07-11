<?php

namespace App\RfcFetcher;

use App\RfcFetcher\Entity\Link;
use Symfony\Component\DomCrawler\Crawler;

final class LinkExtractor
{
    const array EXCLUDE_RFCS = [
        // This RFC is empty (TBW).
        'extensionsiberia',
    ];

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
        $implemented = $crawler->filter('h3[id^="php_"] + div a')->each($converter);
        $declined = $crawler->filter('#declined + div a')->each($converter);
        $withdrawn = $crawler->filter('#withdrawn + div a')->each($converter);
        $inactive = $crawler->filter('#inactive + div a')->each($converter);
        $obsolete = $crawler->filter('#obsolete + div a')->each($converter);

        return array_filter([
            ...$votingRfcs,
            ...$underDiscussionRfcs,
            ...$draftRfcs,
            ...$processAndPolicyRfcs,
            ...$pendingImplementationLanding,
            ...$implemented,
            ...$declined,
            ...$withdrawn,
            ...$inactive,
            ...$obsolete,
        ], function (Link $link) use ($host) {
            if (in_array($link->url, array_map(fn($rfc) => "{$host}/rfc/{$rfc}", self::EXCLUDE_RFCS), true)) {
                return false;
            }
            // Filter out links that are not RFC detail pages
            // (e.g., external links or other wiki pages)
            return str_starts_with($link->url, "{$host}/rfc/");
        });
    }
}
