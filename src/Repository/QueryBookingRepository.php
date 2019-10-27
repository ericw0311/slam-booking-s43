<?php

namespace App\Repository;

use App\Entity\QueryBooking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method QueryBooking|null find($id, $lockMode = null, $lockVersion = null)
 * @method QueryBooking|null findOneBy(array $criteria, array $orderBy = null)
 * @method QueryBooking[]    findAll()
 * @method QueryBooking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QueryBookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QueryBooking::class);
    }

    // /**
    //  * @return QueryBooking[] Returns an array of QueryBooking objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?QueryBooking
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
