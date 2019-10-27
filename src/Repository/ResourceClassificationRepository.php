<?php

namespace App\Repository;

use App\Entity\ResourceClassification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ResourceClassification|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResourceClassification|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResourceClassification[]    findAll()
 * @method ResourceClassification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResourceClassificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResourceClassification::class);
    }

    // /**
    //  * @return ResourceClassification[] Returns an array of ResourceClassification objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ResourceClassification
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
