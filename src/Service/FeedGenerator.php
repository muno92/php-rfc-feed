<?php

namespace App\Service;

use App\Entity\Activity;
use App\Repository\ActivityRepository;
use DOMDocument;
use DOMElement;

class FeedGenerator
{
    private const FEED_TITLE = 'PHP RFC Feed';
    private const FEED_DESCRIPTION = 'Latest PHP RFC status updates';
    private const FEED_AUTHOR = 'muno92';
    private const GITHUB_REPO = 'https://github.com/muno92/php-rfc-feed';

    private const FEED_URL = 'https://php-rfc-feed.muno92.dev/feed.xml';

    public function __construct(
        private readonly ActivityRepository $activityRepository
    ) {
    }

    public function generateFeed(int $limit = 10): string
    {
        $activities = $this->activityRepository->findLatestActivities($limit);
        
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        
        $feed = $this->createFeedElement($dom);
        $dom->appendChild($feed);
        
        foreach ($activities as $activity) {
            $entry = $this->createEntryElement($dom, $activity);
            $feed->appendChild($entry);
        }
        
        return $dom->saveXML();
    }

    private function createFeedElement(DOMDocument $dom): DOMElement
    {
        $feed = $dom->createElement('feed');
        $feed->setAttribute('xmlns', 'http://www.w3.org/2005/Atom');
        
        $feed->appendChild($dom->createElement('title', self::FEED_TITLE));
        $feed->appendChild($dom->createElement('subtitle', self::FEED_DESCRIPTION));
        
        // Main link to PHP RFC page
        $link = $dom->createElement('link');
        $link->setAttribute('href', self::GITHUB_REPO);
        $link->setAttribute('rel', 'alternate');
        $link->setAttribute('type', 'text/html');
        $feed->appendChild($link);
        
        // Self link to the feed
        $selfLink = $dom->createElement('link');
        $selfLink->setAttribute('href', self::FEED_URL);
        $selfLink->setAttribute('rel', 'self');
        $selfLink->setAttribute('type', 'application/atom+xml');
        $feed->appendChild($selfLink);
        
        $feed->appendChild($dom->createElement('id', self::GITHUB_REPO));
        $feed->appendChild($dom->createElement('updated', (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM)));
        
        $author = $dom->createElement('author');
        $author->appendChild($dom->createElement('name', self::FEED_AUTHOR));
        $author->appendChild($dom->createElement('uri', self::GITHUB_REPO));
        $feed->appendChild($author);
        
        return $feed;
    }

    private function createEntryElement(DOMDocument $dom, Activity $activity): DOMElement
    {
        $entry = $dom->createElement('entry');
        
        $rfc = $activity->getRfc();
        $title = sprintf('[%s] %s', $activity->getStatus(), $rfc->getTitle());
        $entry->appendChild($dom->createElement('title', htmlspecialchars($title)));
        
        $link = $dom->createElement('link');
        $link->setAttribute('href', $rfc->getUrl());
        $link->setAttribute('rel', 'alternate');
        $link->setAttribute('type', 'text/html');
        $entry->appendChild($link);
        
        $entry->appendChild($dom->createElement('id', $rfc->getUrl()));
        $entry->appendChild($dom->createElement('updated', $activity->getCreatedAt()->format(\DateTimeInterface::ATOM)));
        $entry->appendChild($dom->createElement('published', $activity->getCreatedAt()->format(\DateTimeInterface::ATOM)));

        // add url to summary
        $summary = $dom->createElement('summary');
        $summary->setAttribute('type', 'html');
        $summary->appendChild($dom->createTextNode($rfc->getUrl()));
        $entry->appendChild($summary);

        return $entry;
    }
}
