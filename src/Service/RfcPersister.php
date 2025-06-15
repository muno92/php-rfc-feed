<?php

namespace App\Service;

use App\Entity\Activity;
use App\Entity\Rfc;
use App\Repository\RfcRepository;
use App\RfcFetcher\Entity\RfcDetail;
use Doctrine\ORM\EntityManagerInterface;

class RfcPersister
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RfcRepository $rfcRepository
    ) {
    }

    /**
     * Save RFC information to the database if it's new or has been updated
     */
    public function saveRfc(string $url, RfcDetail $rfcDetail): ?Activity
    {
        $rfc = $this->rfcRepository->findOneByUrl($url);
        $isNewRfc = false;

        // If RFC doesn't exist yet, create a new one
        if (!$rfc) {
            $rfc = new Rfc();
            $rfc->setUrl($url);
            $rfc->setTitle($rfcDetail->title);
            $rfc->setVersion($rfcDetail->version);
            $isNewRfc = true;
        }

        // Check if the status has changed
        $latestActivity = $rfc->getLatestActivity();
        $statusChanged = $isNewRfc || $latestActivity === null || $latestActivity->getStatus() !== $rfcDetail->status;

        // Only create a new activity if it's a new RFC or the status has changed
        if ($statusChanged) {
            $activity = new Activity();
            $activity->setStatus($rfcDetail->status);
            $activity->setCreatedAt($rfcDetail->lastUpdated);
            $rfc->addActivity($activity);
            
            $this->entityManager->persist($rfc);
            $this->entityManager->persist($activity);
            $this->entityManager->flush();
            
            return $activity;
        }

        return null;
    }
}
