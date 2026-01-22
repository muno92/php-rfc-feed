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
            $rfc->setVersion($rfcDetail->version);
            $isNewRfc = true;
        }

        $latestActivity = $rfc->getLatestActivity();

        if ($this->rfcStatusIsChanged($isNewRfc, $latestActivity, $rfcDetail)) {
            $activity = new Activity();
            $activity->setTitle($rfcDetail->title);
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

    /**
     * @param bool $isNewRfc
     * @param Activity|null $latestActivity
     * @param RfcDetail $rfcDetail
     * @return bool
     */
    private function rfcStatusIsChanged(bool $isNewRfc, ?Activity $latestActivity, RfcDetail $rfcDetail): bool
    {
        if ($isNewRfc) {
            return true;
        }
        if ($latestActivity?->getStatus() === $rfcDetail->status) {
            return false;
        }
        return true;
    }
}
