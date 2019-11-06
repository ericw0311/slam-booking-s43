<?php
namespace App\Repository;

use App\Entity\PlanificationLine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;

/**
 * @method PlanificationLine|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanificationLine|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanificationLine[]    findAll()
 * @method PlanificationLine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanificationLineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanificationLine::class);
    }

    // Recherche les lignes d'une periode de planification
  	public function getLines($planificationPeriod)
      {
      $qb = $this->createQueryBuilder('pl');
      $qb->where('pl.planificationPeriod = :p')->setParameter('p', $planificationPeriod);
      $qb->orderBy('pl.oorder', 'ASC');

      $query = $qb->getQuery();
      $results = $query->getResult();
      return $results;
      }

  	// Compte les periodes de planification d'une grille horaire
      public function getPlanificationPeriodsCount($timetable)
      {
      $qb = $this->createQueryBuilder('pl');
      $qb->select($queryBuilder->expr()->count('pl'));
      $qb->where('pl.timetable = :timetable')->setParameter('timetable', $timetable);

      $query = $qb->getQuery();
      $singleScalar = $query->getSingleScalarResult();
      return $singleScalar;
      }

    // /**
    //  * @return PlanificationLine[] Returns an array of PlanificationLine objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PlanificationLine
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
