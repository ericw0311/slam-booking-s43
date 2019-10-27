<?php

namespace App\Repository;

use App\Entity\PlanificationView;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PlanificationView|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanificationView|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanificationView[]    findAll()
 * @method PlanificationView[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanificationViewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanificationView::class);
    }

    // /**
    //  * @return PlanificationView[] Returns an array of PlanificationView objects
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
    public function findOneBySomeField($value): ?PlanificationView
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
