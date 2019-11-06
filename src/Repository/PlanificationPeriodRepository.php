<?php
namespace App\Repository;

use App\Entity\PlanificationPeriod;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;

/**
 * @method PlanificationPeriod|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanificationPeriod|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanificationPeriod[]    findAll()
 * @method PlanificationPeriod[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanificationPeriodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanificationPeriod::class);
    }

    // Retourne les periodes de planification d'une ressource
    public function getResourcePlanificationPeriods($resource)
    {
	$qb = $this->createQueryBuilder('pp');
	$qb->innerJoin('pp.planificationResources', 'pr', Expr\Join::WITH, $qb->expr()->eq('pr.resource', '?1'));
	$qb->orderBy('pp.id', 'ASC');
	$qb->setParameter(1, $resource);

	$query = $qb->getQuery();
	$results = $query->getResult();
	return $results;
    }

    // Retourne les periodes de planification d'une grille horaire
    public function getTimetablePlanificationPeriods($timetable)
    {
	$qb = $this->createQueryBuilder('pp');
	$qb->innerJoin('pp.planificationLines', 'pl', Expr\Join::WITH, $qb->expr()->eq('pl.timetable', '?1'));
	$qb->groupBy('pp.id');
	$qb->orderBy('pp.id', 'ASC');
	$qb->setParameter(1, $timetable);

	$query = $qb->getQuery();
	$results = $query->getResult();
	return $results;
    }

	// Retourne la periode de planification precedente
	public function getPreviousPlanificationPeriod($planification, $planificationPeriodID)
    {
	$qb = $this->createQueryBuilder('pp');
	$qb->where('pp.planification = :planification')->setParameter('planification', $planification);
	$qb->andWhere('pp.id < :planificationPeriodID')->setParameter('planificationPeriodID', $planificationPeriodID);
	$qb->orderBy('pp.id', 'DESC');
	$qb->setMaxResults(1);

	$query = $qb->getQuery();
	$results = $query->getOneOrNullResult();
	return $results;
	}

	// Retourne la periode de planification suivante
	public function getNextPlanificationPeriod($planification, $planificationPeriodID)
    {
	$qb = $this->createQueryBuilder('pp');
	$qb->where('pp.planification = :planification')->setParameter('planification', $planification);
	$qb->andWhere('pp.id > :planificationPeriodID')->setParameter('planificationPeriodID', $planificationPeriodID);
	$qb->orderBy('pp.id', 'ASC');
	$qb->setMaxResults(1);

	$query = $qb->getQuery();
	$results = $query->getOneOrNullResult();
	return $results;
	}

	// Retourne la derniere periode d'une planification
	public function getLastPlanificationPeriod($planification)
    {
	$qb = $this->createQueryBuilder('pp');
	$qb->where('pp.planification = :planification')->setParameter('planification', $planification);
	$qb->orderBy('pp.id', 'DESC');
	$qb->setMaxResults(1);

	$query = $qb->getQuery();
	$results = $query->getOneOrNullResult();
	return $results;
	}

	// Retourne la periode de planification en fonction d'une planification et une date
	public function getPlanificationPeriod($planification, \Datetime $date)
    {
	$qb = $this->createQueryBuilder('pp');
	$qb->where('pp.planification = :planification');
	$qb->andWhere($qb->expr()->andX(
			$qb->expr()->orX($qb->expr()->isNull('pp.beginningDate'), $qb->expr()->lte('pp.beginningDate', ':beginningDate')),
			$qb->expr()->orX($qb->expr()->isNull('pp.endDate'), $qb->expr()->gte('pp.endDate', ':endDate'))));
	$qb->setParameter('planification', $planification);
	$qb->setParameter('beginningDate', $date);
	$qb->setParameter('endDate', $date);
	$qb->orderBy('pp.id', 'ASC');
	$qb->setMaxResults(1);

	$query = $qb->getQuery();
	$results = $query->getOneOrNullResult();
	return $results;
	}
}
