<?php

namespace App\Repository;

use App\Entity\BookingDuplication;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method BookingDuplication|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookingDuplication|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookingDuplication[]    findAll()
 * @method BookingDuplication[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingDuplicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookingDuplication::class);
    }

    // /**
    //  * @return BookingDuplication[] Returns an array of BookingDuplication objects
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
    public function findOneBySomeField($value): ?BookingDuplication
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
