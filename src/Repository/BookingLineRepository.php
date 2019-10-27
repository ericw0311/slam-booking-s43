<?php

namespace App\Repository;

use App\Entity\BookingLine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method BookingLine|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookingLine|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookingLine[]    findAll()
 * @method BookingLine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingLineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookingLine::class);
    }

    // /**
    //  * @return BookingLine[] Returns an array of BookingLine objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BookingLine
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
