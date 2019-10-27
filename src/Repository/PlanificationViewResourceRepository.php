<?php

namespace App\Repository;

use App\Entity\PlanificationViewResource;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PlanificationViewResource|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanificationViewResource|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanificationViewResource[]    findAll()
 * @method PlanificationViewResource[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanificationViewResourceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanificationViewResource::class);
    }

    // /**
    //  * @return PlanificationViewResource[] Returns an array of PlanificationViewResource objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PlanificationViewResource
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
