<?php

namespace App\Repository;

use App\Entity\InnovationUserFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method InnovationUserFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method InnovationUserFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method InnovationUserFile[]    findAll()
 * @method InnovationUserFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InnovationUserFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InnovationUserFile::class);
    }

    // /**
    //  * @return InnovationUserFile[] Returns an array of InnovationUserFile objects
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
    public function findOneBySomeField($value): ?InnovationUserFile
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
