<?php

namespace App\Tests\Fixtures;

use App\Entity\Activity;
use App\Entity\Rfc;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RfcFixtures extends Fixture
{
    public const RFC_EXAMPLE = 'rfc-example';
    public const RFC_EXAMPLE2 = 'rfc-example2';
    public const RFC_EXAMPLE3 = 'rfc-example3';

    public function load(ObjectManager $manager): void
    {
        // Example RFC 1 - New RFC
        $rfc1 = new Rfc();
        $rfc1->setTitle('Example RFC');
        $rfc1->setUrl('https://wiki.php.net/rfc/example');
        
        $manager->persist($rfc1);
        $this->addReference(self::RFC_EXAMPLE, $rfc1);

        // Example RFC 2 - With Under Discussion status
        $rfc2 = new Rfc();
        $rfc2->setTitle('Example RFC 2');
        $rfc2->setUrl('https://wiki.php.net/rfc/example2');
        
        $activity2 = new Activity();
        $activity2->setStatus('Under Discussion');
        $activity2->setCreatedAt(new \DateTimeImmutable('-1 day'));
        $activity2->setRfc($rfc2);
        $rfc2->addActivity($activity2);
        
        $manager->persist($rfc2);
        $manager->persist($activity2);
        $this->addReference(self::RFC_EXAMPLE2, $rfc2);

        // Example RFC 3 - With Under Discussion status (for no-update test)
        $rfc3 = new Rfc();
        $rfc3->setTitle('Example RFC 3');
        $rfc3->setUrl('https://wiki.php.net/rfc/example3');
        
        $activity3 = new Activity();
        $activity3->setStatus('Under Discussion');
        $activity3->setCreatedAt(new \DateTimeImmutable('-1 day'));
        $activity3->setRfc($rfc3);
        $rfc3->addActivity($activity3);
        
        $manager->persist($rfc3);
        $manager->persist($activity3);
        $this->addReference(self::RFC_EXAMPLE3, $rfc3);

        $manager->flush();
    }
}