<?php

namespace App\Repository;

use App\Entity\TimetableLine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method TimetableLine|null find($id, $lockMode = null, $lockVersion = null)
 * @method TimetableLine|null findOneBy(array $criteria, array $orderBy = null)
 * @method TimetableLine[]    findAll()
 * @method TimetableLine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimetableLineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TimetableLine::class);
    }

    // /**
    //  * @return TimetableLine[] Returns an array of TimetableLine objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TimetableLine
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
