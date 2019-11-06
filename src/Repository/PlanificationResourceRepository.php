<?php
namespace App\Repository;

use App\Entity\PlanificationResource;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;

/**
 * @method PlanificationResource|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanificationResource|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanificationResource[]    findAll()
 * @method PlanificationResource[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanificationResourceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanificationResource::class);
    }

    // Recherche les ressources d'une periode de planification
  	public function getResources(\App\Entity\PlanificationPeriod $planificationPeriod)
      {
      $qb = $this->createQueryBuilder('pr');
      $qb->where('pr.planificationPeriod = :planificationPeriod')->setParameter('planificationPeriod', $planificationPeriod);
      $qb->orderBy('pr.oorder', 'ASC');

      $query = $qb->getQuery();
      $results = $query->getResult();
      return $results;
      }

  	// Recherche une ressource parmi les ressources d'une periode de planification (utilisé en duplication de réservation)
  	public function getResource(\App\Entity\PlanificationPeriod $planificationPeriod, \App\Entity\Resource $resource)
      {
      $qb = $this->createQueryBuilder('pr');
      $qb->where('pr.planificationPeriod = :planificationPeriod')->setParameter('planificationPeriod', $planificationPeriod);
      $qb->andWhere('pr.resource = :resource')->setParameter('resource', $resource);
      $qb->orderBy('pr.oorder', 'ASC');

      $query = $qb->getQuery();
      $results = $query->getResult();
      return $results;
      }

  	// Compte les periodes de planification d'une ressource
      public function getPlanificationPeriodsCount($resource)
      {
      $qb = $this->createQueryBuilder('pr');
      $qb->select($qb->expr()->count('pr'));
      $qb->where('pr.resource = :resource')->setParameter('resource', $resource);

      $query = $qb->getQuery();
      $singleScalar = $query->getSingleScalarResult();
      return $singleScalar;
      }

  	// Construit le Query Builder d'une ressource planifiee
  	public function getResourcePlanifiedQB()
      {
      $qb = $this->createQueryBuilder('pr');
      $qb->where('pr.resource = r.id');
      return $qb;
      }

  	// Construit le Query Builder d'une ressource planifiee en dehors de la periode transmise
  	public function getResourcePlanifiedExcludePeriodQB(\App\Entity\PlanificationPeriod $planificationPeriod)
      {
      $qb = $this->createQueryBuilder('pr');
      $qb->where('pr.resource = r.id and pr.planificationPeriod <> '.$planificationPeriod->getID());
      return $qb;
      }
}
