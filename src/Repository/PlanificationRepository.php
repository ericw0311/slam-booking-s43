<?php
namespace App\Repository;

use App\Entity\Planification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;

/**
 * @method Planification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Planification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Planification[]    findAll()
 * @method Planification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Planification::class);
    }

    // 1) Toutes les planifications
      public function getPlanificationsCount($file)
      {
      $queryBuilder = $this->createQueryBuilder('p');
      $queryBuilder->select($queryBuilder->expr()->count('p'));
      $queryBuilder->where('p.file = :file')->setParameter('file', $file);

      $query = $queryBuilder->getQuery();
      $singleScalar = $query->getSingleScalarResult();
      return $singleScalar;
      }

      public function getDisplayedPlanifications($file, $firstRecordIndex, $maxRecord)
      {
      $qb = $this->createQueryBuilder('p');
      $qb->where('p.file = :file')->setParameter('file', $file);
      $qb->orderBy('p.type', 'ASC');
      $qb->addOrderBy('p.internal', 'DESC');
      $qb->addOrderBy('p.code', 'ASC');
      $qb->addOrderBy('p.name', 'ASC');
      $qb->setFirstResult($firstRecordIndex);
      $qb->setMaxResults($maxRecord);

      $query = $qb->getQuery();
      $results = $query->getResult();
      return $results;
      }

  	// 2) Planifications affichées dans le planning

  	// Nombre de planifications affichées dans le planning
  	public function getPlanningPlanificationsCount($file, \Datetime $date)
  	{
  	$qb = $this->createQueryBuilder('p');
  	$qb->select($qb->expr()->count('p'));
  	$qb->where('p.file = :file')->setParameter('file', $file);
  	$this->getPlanningPlanificationsPeriod($qb);
  	$this->getPlanningPlanificationsPeriodParameters($qb, $date);

      $query = $qb->getQuery();
      $singleScalar = $query->getSingleScalarResult();
      return $singleScalar;
  	}

  	// Retourne la première planification affichée dans le planning
  	public function getFirstPlanningPlanification($file, \Datetime $date)
      {
      $qb = $this->createQueryBuilder('p');
      $qb->select('p.id planificationID');
      $qb->addSelect('pp.id planificationPeriodID');
      $qb->where('p.file = :file')->setParameter('file', $file);
  	$this->getPlanningPlanificationsPeriod($qb);
  	$this->getPlanificationsSort($qb);
  	$this->getPlanningPlanificationsPeriodParameters($qb, $date);

  	$qb->setMaxResults(1);
      $query = $qb->getQuery();
  	$results = $query->getSingleResult();
      return $results;
      }

      public function getPlanningPlanifications($file, \Datetime $date)
      {
        $qb = $this->createQueryBuilder('p');
        $qb->select('p.id ID');
        $qb->addSelect('p.type');
        $qb->addSelect('p.name');
        $qb->addSelect('p.internal');
        $qb->addSelect('p.code');
        $qb->addSelect('pp.id planificationPeriodID');
        $qb->where('p.file = :file')->setParameter('file', $file);
        $this->getPlanningPlanificationsPeriod($qb);
        $this->getPlanificationsSort($qb);
        $this->getPlanningPlanificationsPeriodParameters($qb, $date);

        $query = $qb->getQuery();
        $results = $query->getResult();
        return $results;
      }

      // Planifications affichées dans le planning et accessibles par l'utilisateur connecté
      public function getPlanningUserFilePlanifications($file, \Datetime $date, $planificationPeriodUserFileQB)
      {
        $qb = $this->createQueryBuilder('p');
        $qb->select('p.id ID');
        $qb->addSelect('p.type');
        $qb->addSelect('p.name');
        $qb->addSelect('p.internal');
        $qb->addSelect('p.code');
        $qb->addSelect('pp.id planificationPeriodID');
        $qb->where('p.file = :file')->setParameter('file', $file);
        $this->getPlanningPlanificationsPeriod($qb);
        $qb->andWhere($qb->expr()->exists($planificationPeriodUserFileQB->getDQL()));

        $this->getPlanificationsSort($qb);
        $this->getPlanningPlanificationsPeriodParameters($qb, $date);

        $query = $qb->getQuery();
        $results = $query->getResult();
        return $results;
      }

  	// Planifications affichées dans le planning: période
  	public function getPlanningPlanificationsPeriod($qb)
      {
  	$qb->innerJoin('p.planificationPeriods', 'pp', Expr\Join::WITH,
  		$qb->expr()->andX(
  			$qb->expr()->orX($qb->expr()->isNull('pp.beginningDate'), $qb->expr()->lte('pp.beginningDate', ':beginningDate')),
  			$qb->expr()->orX($qb->expr()->isNull('pp.endDate'), $qb->expr()->gte('pp.endDate', ':endDate'))));
      }

  	// Planifications affichées dans le planning: paramètres de la période
  	public function getPlanningPlanificationsPeriodParameters($qb, \Datetime $date)
      {
  	$qb->setParameter('beginningDate', $date->format('Ymd'));
  	$qb->setParameter('endDate', $date->format('Ymd'));
      }

  	// 3) Planifications faisant référence à une grille horaire

      public function getTimetablePlanificationsCount($file, $timetable)
      {
      $qb = $this->createQueryBuilder('p');
      $qb->select($qb->expr()->count('p'));
      $qb->where('p.file = :file')->setParameter('file', $file);
  	$this->getTimetablePlanifications($qb, $timetable);

      $query = $qb->getQuery();
      $singleScalar = $query->getSingleScalarResult();
      return $singleScalar;
      }

      public function getTimetablePlanificationsList($file, $timetable)
      {
      $qb = $this->createQueryBuilder('p');
      $qb->where('p.file = :file')->setParameter('file', $file);
  	$this->getTimetablePlanifications($qb, $timetable);
  	$this->getPlanificationsSort($qb);

      $query = $qb->getQuery();
      $results = $query->getResult();
      return $results;
      }

  	public function getTimetablePlanifications($qb, $timetable)
      {
  	$qb->innerJoin('p.planificationPeriods', 'pp');
  	$qb->innerJoin('pp.planificationLines', 'pl', Expr\Join::WITH, $qb->expr()->eq('pl.timetable', ':t'));
      $qb->setParameter('t', $timetable);
      }

  	// 4) Planifications faisant référence à une ressource

      public function getResourcePlanificationsCount($file, $resource)
      {
      $qb = $this->createQueryBuilder('p');
      $qb->select($qb->expr()->count('p'));
      $qb->where('p.file = :file')->setParameter('file', $file);
  	$this->getResourcePlanifications($qb, $resource);

      $query = $qb->getQuery();
      $singleScalar = $query->getSingleScalarResult();
      return $singleScalar;
      }

      public function getResourcePlanificationsList($file, $resource)
      {
      $qb = $this->createQueryBuilder('p');
      $qb->where('p.file = :file')->setParameter('file', $file);
  	$this->getResourcePlanifications($qb, $resource);
  	$this->getPlanificationsSort($qb);

      $query = $qb->getQuery();
      $results = $query->getResult();
      return $results;
      }

  	public function getResourcePlanifications($qb, $resource)
      {
  	$qb->innerJoin('p.planificationPeriods', 'pp');
  	$qb->innerJoin('pp.planificationResources', 'pr', Expr\Join::WITH, $qb->expr()->eq('pr.resource', ':r'));
      $qb->setParameter('r', $resource);
      }

  	// Tri des planifications
  	public function getPlanificationsSort($qb)
      {
      $qb->orderBy('p.type', 'ASC');
      $qb->addOrderBy('p.internal', 'DESC');
      $qb->addOrderBy('p.code', 'ASC');
      $qb->addOrderBy('p.name', 'ASC');
      }
}
