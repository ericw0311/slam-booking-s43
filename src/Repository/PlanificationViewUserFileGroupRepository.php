<?php
namespace App\Repository;

use App\Entity\PlanificationViewUserFileGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;

/**
 * @method PlanificationViewUserFileGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanificationViewUserFileGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanificationViewUserFileGroup[]    findAll()
 * @method PlanificationViewUserFileGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanificationViewUserFileGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanificationViewUserFileGroup::class);
    }

    // Recherche les vues d'une periode de planification
    public function getViews(\App\Entity\PlanificationPeriod $planificationPeriod)
    {
        $qb = $this->createQueryBuilder('pvufg');
        $qb->where('pvufg.planificationPeriod = :planificationPeriod')->setParameter('planificationPeriod', $planificationPeriod);
        $qb->innerJoin('pvufg.userFileGroup', 'ufg');
        $qb->orderBy('ufg.type', 'DESC');
        $qb->addOrderBy('pvufg.oorder', 'ASC');

        $query = $qb->getQuery();
        $results = $query->getResult();
        return $results;
    }

    // Construit le Query Builder des vues d'une période de planification
    public function getUserFileGroupsInPlanificationViewUFG_QB(\App\Entity\PlanificationPeriod $planificationPeriod)
    {
        $qb = $this->createQueryBuilder('pvufg');
        $qb->where('pvufg.userFileGroup = ufg.id and pvufg.planificationPeriod = '.$planificationPeriod->getID());
        return $qb;
    }

    // Retourne la premiere vue d'une période de planification affichée. Les vues liées à un groupe d'utilisateurs de type MANUAL sont affichées en premier puis celle liée au groupe d'utilisateurs de type ALL, d'où le tri décroissant sur le type de groupe d'utilisateurs
    public function getFirstPlanificationViewUFG(\App\Entity\PlanificationPeriod $planificationPeriod)
    {
        $qb = $this->createQueryBuilder('pvufg');
        $qb->where('pvufg.planificationPeriod = :planificationPeriod')->setParameter('planificationPeriod', $planificationPeriod);
        $qb->innerJoin('pvufg.userFileGroup', 'ufg');
        $qb->orderBy('ufg.type', 'DESC');
        $qb->addOrderBy('pvufg.oorder', 'ASC');
        $qb->setMaxResults(1);
        $query = $qb->getQuery();
        $results = $query->getOneOrNullResult();
        return $results;
    }

    // Retourne la vue precedente
    public function getPreviousPlanificationViewUFG(\App\Entity\PlanificationPeriod $planificationPeriod, \App\Entity\PlanificationViewUserFileGroup $planificationViewUserFileGroup)
    {
        $qb = $this->createQueryBuilder('pvufg');
        $qb->where('pvufg.planificationPeriod = :planificationPeriod')->setParameter('planificationPeriod', $planificationPeriod);
        $qb->andWhere('pvufg.oorder < :oorder')->setParameter('oorder', $planificationViewUserFileGroup->getOrder());
        $qb->orderBy('pvufg.oorder', 'DESC');
        $qb->setMaxResults(1);
        $query = $qb->getQuery();
        $results = $query->getOneOrNullResult();
        return $results;
    }

    // Retourne la vue suivante
    public function getNextPlanificationViewUFG(\App\Entity\PlanificationPeriod $planificationPeriod, \App\Entity\PlanificationViewUserFileGroup $planificationViewUserFileGroup)
    {
        $qb = $this->createQueryBuilder('pvufg');
        $qb->where('pvufg.planificationPeriod = :planificationPeriod')->setParameter('planificationPeriod', $planificationPeriod);
        $qb->andWhere('pvufg.oorder > :oorder')->setParameter('oorder', $planificationViewUserFileGroup->getOrder());
        $qb->orderBy('pvufg.oorder', 'ASC');
        $qb->setMaxResults(1);
        $query = $qb->getQuery();
        $results = $query->getOneOrNullResult();
        return $results;
    }

    public function getManualPlanificationViewUFGCount(\App\Entity\PlanificationPeriod $planificationPeriod)
    {
        $qb = $this->createQueryBuilder('pvufg');
        $qb->select($qb->expr()->count('pvufg'));
        $qb->where('pvufg.planificationPeriod = :planificationPeriod')->setParameter('planificationPeriod', $planificationPeriod);
        $qb->andWhere('ufg.type = :type')->setParameter('type', 'MANUAL');
        $qb->innerJoin('pvufg.userFileGroup', 'ufg');
        $query = $qb->getQuery();
        $singleScalar = $query->getSingleScalarResult();
        return $singleScalar;
    }

    public function getMinManualPlanificationViewUFGOrder(\App\Entity\PlanificationPeriod $planificationPeriod)
    {
        $qb = $this->createQueryBuilder('pvufg');
        $qb->select($qb->expr()->min('pvufg.oorder'));
        $qb->where('pvufg.planificationPeriod = :planificationPeriod')->setParameter('planificationPeriod', $planificationPeriod);
        $qb->andWhere('ufg.type = :type')->setParameter('type', 'MANUAL');
        $qb->innerJoin('pvufg.userFileGroup', 'ufg');

        $query = $qb->getQuery();
        $singleScalar = $query->getSingleScalarResult();
        if ($singleScalar == null) {
            $singleScalar = 0;
        }
        return $singleScalar;
    }

    public function getMaxManualPlanificationViewUFGOrder(\App\Entity\PlanificationPeriod $planificationPeriod)
    {
        $qb = $this->createQueryBuilder('pvufg');
        $qb->select($qb->expr()->max('pvufg.oorder'));
        $qb->where('pvufg.planificationPeriod = :planificationPeriod')->setParameter('planificationPeriod', $planificationPeriod);
        $qb->andWhere('ufg.type = :type')->setParameter('type', 'MANUAL');
        $qb->innerJoin('pvufg.userFileGroup', 'ufg');

        $query = $qb->getQuery();
        $singleScalar = $query->getSingleScalarResult();
        if ($singleScalar == null) {
            $singleScalar = 0;
        }
        return $singleScalar;
    }

    public function getMaxPlanificationViewUFGOrder(\App\Entity\PlanificationPeriod $planificationPeriod)
    {
        $qb = $this->createQueryBuilder('pvufg');
        $qb->select($qb->expr()->max('pvufg.oorder'));
        $qb->where('pvufg.planificationPeriod = :planificationPeriod')->setParameter('planificationPeriod', $planificationPeriod);

        $query = $qb->getQuery();
        $singleScalar = $query->getSingleScalarResult();
        return $singleScalar;
    }

    // Retourne la premiere vue d'une période de planification liée à un utilisateur dossier
    public function getUserFileFirstPlanificationView(\App\Entity\PlanificationPeriod $planificationPeriod, \App\Entity\UserFile $userFile)
    {
      $qb = $this->createQueryBuilder('pvufg');
      $qb->where('pvufg.planificationPeriod = :planificationPeriod')->setParameter('planificationPeriod', $planificationPeriod);
      $qb->andWhere('pvufg.active = :active')->setParameter('active', 1);
      $qb->innerJoin('pvufg.userFileGroup', 'ufg');
      $qb->innerJoin('ufg.userFiles', 'uf', Expr\Join::WITH, $qb->expr()->eq('uf.id','?1'))->setParameter(1, $userFile->getId());

      $qb->orderBy('pvufg.oorder', 'ASC');
      $qb->setMaxResults(1);
      $query = $qb->getQuery();
      $results = $query->getOneOrNullResult();
      return $results;
    }

    // Construit le Query Builder d'une période de planification accessible pour un utilisateur dossier
    public function getPlanificationPeriodUserFileQB(\App\Entity\UserFile $userFile)
    {
      $qb = $this->createQueryBuilder('pvufg');
      $qb->where('pvufg.active = 1');
      $qb->andWhere('pvufg.planificationPeriod = pp.id');
      $qb->innerJoin('pvufg.userFileGroup', 'ufg');
      $qb->innerJoin('ufg.userFiles', 'uf', Expr\Join::WITH, 'uf.id = '.$userFile->getId());
      return $qb;
    }
}
