<?php

namespace App\Repository;

use App\Entity\Rfc;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Rfc>
 *
 * @method Rfc|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rfc|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rfc[]    findAll()
 * @method Rfc[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RfcRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rfc::class);
    }

    public function save(Rfc $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Rfc $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneByUrl(string $url): ?Rfc
    {
        return $this->findOneBy(['url' => $url]);
    }

    /**
     * @return Rfc[] Returns an array of Rfc objects with their latest activities
     */
    public function findLatestWithActivities(int $limit = 10): array
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.activities', 'a')
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}