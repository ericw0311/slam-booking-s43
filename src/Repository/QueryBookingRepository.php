<?php
namespace App\Repository;

use App\Entity\QueryBooking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;

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

    public function getQueryBookingCount(\App\Entity\File $file)
      {
      $queryBuilder = $this->createQueryBuilder('qb');
      $queryBuilder->select($queryBuilder->expr()->count('qb'));
      $queryBuilder->where('qb.file = :file')->setParameter('file', $file);
      $query = $queryBuilder->getQuery();
      $singleScalar = $query->getSingleScalarResult();
      return $singleScalar;
      }

      public function getDisplayedQueryBooking(\App\Entity\File $file, $firstRecordIndex, $maxRecord)
      {
      $queryBuilder = $this->createQueryBuilder('qb');
      $queryBuilder->where('qb.file = :file')->setParameter('file', $file);
      $queryBuilder->orderBy('qb.name', 'ASC');
      $queryBuilder->setFirstResult($firstRecordIndex);
      $queryBuilder->setMaxResults($maxRecord);

      $query = $queryBuilder->getQuery();
      $results = $query->getResult();
      return $results;
      }
}
