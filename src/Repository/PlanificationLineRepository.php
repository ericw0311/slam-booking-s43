<?php

namespace App\Repository;

use App\Entity\PlanificationLine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PlanificationLine|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanificationLine|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanificationLine[]    findAll()
 * @method PlanificationLine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanificationLineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanificationLine::class);
    }

    // /**
    //  * @return PlanificationLine[] Returns an array of PlanificationLine objects
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
    public function findOneBySomeField($value): ?PlanificationLine
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
