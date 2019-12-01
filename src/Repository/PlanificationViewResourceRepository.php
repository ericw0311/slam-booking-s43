<?php
namespace App\Repository;

use App\Entity\PlanificationViewResource;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PlanificationViewResource|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanificationViewResource|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanificationViewResource[]    findAll()
 * @method PlanificationViewResource[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanificationViewResourceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanificationViewResource::class);
    }

    // Recherche les ressources actives pour un groupe d'utilisateurs
    public function getUserFileResources(\App\Entity\PlanificationViewUserFileGroup $planificationViewUserFileGroup)
    {
      $qb = $this->createQueryBuilder('pvr');
      $qb->where('pvr.planificationViewUserFileGroup = :planificationViewUFG_ID')->setParameter('planificationViewUFG_ID', $planificationViewUserFileGroup->getID());
      $qb->andWhere('pvr.active = :active')->setParameter('active', 1);
      $qb->innerJoin('pvr.planificationResource', 'pr');
      $qb->orderBy('pr.oorder', 'ASC');

      $query = $qb->getQuery();
      $results = $query->getResult();
      return $results;
    }
}
