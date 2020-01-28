<?php

namespace App\Repository;

use App\Entity\Innovation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Innovation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Innovation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Innovation[]    findAll()
 * @method Innovation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InnovationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Innovation::class);
    }

    // /**
    //  * @return Innovation[] Returns an array of Innovation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Innovation
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
