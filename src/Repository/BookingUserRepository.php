<?php

namespace App\Repository;

use App\Entity\BookingUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method BookingUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookingUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookingUser[]    findAll()
 * @method BookingUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookingUser::class);
    }

    // /**
    //  * @return BookingUser[] Returns an array of BookingUser objects
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
    public function findOneBySomeField($value): ?BookingUser
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
