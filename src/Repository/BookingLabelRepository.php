<?php
namespace App\Repository;

use App\Entity\BookingLabel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method BookingLabel|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookingLabel|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookingLabel[]    findAll()
 * @method BookingLabel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingLabelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookingLabel::class);
    }

    // /**
    //  * @return BookingLabel[] Returns an array of BookingLabel objects
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
    public function findOneBySomeField($value): ?BookingLabel
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
