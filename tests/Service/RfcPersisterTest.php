<?php

namespace App\Tests\Service;

use App\Entity\Activity;
use App\Entity\Rfc;
use App\Repository\RfcRepository;
use App\RfcFetcher\Entity\RfcDetail;
use App\Service\RfcPersister;
use App\Tests\Fixtures\RfcFixtures;
use Doctrine\Bundle\FixturesBundle\Loader\SymfonyFixturesLoader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RfcPersisterTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private RfcRepository $rfcRepository;
    private RfcPersister $persister;

    protected function setUp(): void
    {
        self::bootKernel();
        
        $container = static::getContainer();
        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->rfcRepository = $container->get(RfcRepository::class);
        $this->persister = $container->get(RfcPersister::class);
        
        // Create schema for tests
        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
        
        // Load fixtures
        $this->loadFixtures();
    }
    
    private function loadFixtures(): void
    {
        $loader = new SymfonyFixturesLoader();
        $loader->addFixture(new RfcFixtures());
        
        $purger = new ORMPurger($this->entityManager);
        $executor = new ORMExecutor($this->entityManager, $purger);
        $executor->execute($loader->getFixtures());
    }

    public function testSaveNewRfc(): void
    {
        // Create new RFC detail - using Example 1 from fixtures
        $url = 'https://wiki.php.net/rfc/example';
        $rfcDetail = new RfcDetail('Example RFC', 'Under Discussion', new \DateTimeImmutable(), '1.0');
        
        // Save RFC
        $activity = $this->persister->saveRfc($url, $rfcDetail);
        
        // Assertions
        $this->assertInstanceOf(Activity::class, $activity);
        $this->assertEquals('Under Discussion', $activity->getStatus());
        
        // Verify RFC was saved
        $savedRfc = $this->rfcRepository->findOneByUrl($url);
        $this->assertNotNull($savedRfc);
        $this->assertEquals('Example RFC', $savedRfc->getTitle());
        
        // Verify activity was created
        $activities = $savedRfc->getActivities();
        $this->assertCount(1, $activities);
        $this->assertEquals('Under Discussion', $activities->first()->getStatus());
    }
    
    public function testUpdateExistingRfcWithNewStatus(): void
    {
        // Using Example 2 from fixtures which already has an 'Under Discussion' status
        $url = 'https://wiki.php.net/rfc/example2';
        
        // Update with new status
        $updatedRfcDetail = new RfcDetail('Example RFC 2', 'Implemented', new \DateTimeImmutable(), '1.0');
        $activity = $this->persister->saveRfc($url, $updatedRfcDetail);
        
        // Assertions
        $this->assertInstanceOf(Activity::class, $activity);
        $this->assertEquals('Implemented', $activity->getStatus());
        
        // Verify RFC was updated
        $savedRfc = $this->rfcRepository->findOneByUrl($url);
        $this->assertNotNull($savedRfc);
        
        // Verify both activities exist
        $activities = $savedRfc->getActivities();
        $this->assertCount(2, $activities);
        
        // Convert collection to array for easier testing
        $activityArray = $activities->toArray();
        $statuses = array_map(fn ($a) => $a->getStatus(), $activityArray);
        $this->assertContains('Under Discussion', $statuses);
        $this->assertContains('Implemented', $statuses);
    }
    
    public function testNoUpdateForSameStatus(): void
    {
        // Using Example 3 from fixtures which already has an 'Under Discussion' status
        $url = 'https://wiki.php.net/rfc/example3';
        
        // Get the original activity's created_at timestamp
        $originalRfc = $this->rfcRepository->findOneByUrl($url);
        $originalActivity = $originalRfc->getActivities()->first();
        $originalCreatedAt = $originalActivity->getCreatedAt();
        
        // Try to update with the same status
        $sameStatusDetail = new RfcDetail('Example RFC 3', 'Under Discussion', new \DateTimeImmutable(), '1.0');
        $activity = $this->persister->saveRfc($url, $sameStatusDetail);
        
        // Should not create a new activity
        $this->assertNull($activity);
        
        // Verify only one activity exists
        $savedRfc = $this->rfcRepository->findOneByUrl($url);
        $this->assertNotNull($savedRfc);
        $this->assertCount(1, $savedRfc->getActivities());
        
        // Verify that the created_at timestamp hasn't changed
        $updatedActivity = $savedRfc->getActivities()->first();
        $this->assertEquals($originalCreatedAt, $updatedActivity->getCreatedAt());
    }
    
    protected function tearDown(): void
    {
        // Clean up database
        if (isset($this->entityManager)) {
            // Clear entity manager to avoid issues with cached entities
            $this->entityManager->clear();
            parent::tearDown();
        }
    }
}
