<?php

namespace App\Repository;

use App\Entity\PlanificationResource;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PlanificationResource|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanificationResource|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanificationResource[]    findAll()
 * @method PlanificationResource[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanificationResourceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanificationResource::class);
    }

    // /**
    //  * @return PlanificationResource[] Returns an array of PlanificationResource objects
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
    public function findOneBySomeField($value): ?PlanificationResource
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
