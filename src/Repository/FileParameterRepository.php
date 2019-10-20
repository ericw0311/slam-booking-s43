<?php

namespace App\Repository;

use App\Entity\FileParameter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method FileParameter|null find($id, $lockMode = null, $lockVersion = null)
 * @method FileParameter|null findOneBy(array $criteria, array $orderBy = null)
 * @method FileParameter[]    findAll()
 * @method FileParameter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FileParameterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FileParameter::class);
    }

    // /**
    //  * @return FileParameter[] Returns an array of FileParameter objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FileParameter
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
