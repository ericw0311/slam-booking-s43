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
class PlanificationViewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanificationViewUserFileGroup::class);
    }

    // Recherche les vues d'une periode de planification
    public function getViews(\App\Entity\PlanificationPeriod $planificationPeriod)
    {
        $qb = $this->createQueryBuilder('pv');
        $qb->where('pv.planificationPeriod = :planificationPeriod')->setParameter('planificationPeriod', $planificationPeriod);
        $qb->orderBy('pv.oorder', 'ASC');

        $query = $qb->getQuery();
        $results = $query->getResult();
        return $results;
    }

    // Construit le Query Builder des vues d'une période de planification
    public function getUserFileGroupsInPlanificationViewUFG_QB(\App\Entity\PlanificationPeriod $planificationPeriod)
    {
        $qb = $this->createQueryBuilder('pv');
        $qb->where('pv.userFileGroup = ufg.id and pv.planificationPeriod = '.$planificationPeriod->getID());
        return $qb;
    }

    // Retourne la premiere vue
    public function getFirstPlanificationViewUFG(\App\Entity\PlanificationPeriod $planificationPeriod)
    {
        $qb = $this->createQueryBuilder('pv');
        $qb->where('pv.planificationPeriod = :planificationPeriod')->setParameter('planificationPeriod', $planificationPeriod);
        $qb->orderBy('pv.oorder', 'ASC');
        $qb->setMaxResults(1);
        $query = $qb->getQuery();
        $results = $query->getOneOrNullResult();
        return $results;
    }

    // Retourne la vue precedente
    public function getPreviousPlanificationViewUFG(\App\Entity\PlanificationPeriod $planificationPeriod, \App\Entity\PlanificationViewUserFileGroup $planificationViewUserFileGroup)
    {
        $qb = $this->createQueryBuilder('pv');
        $qb->where('pv.planificationPeriod = :planificationPeriod')->setParameter('planificationPeriod', $planificationPeriod);
        $qb->andWhere('pv.oorder < :oorder')->setParameter('oorder', $planificationViewUserFileGroup->getOrder());
        $qb->orderBy('pv.oorder', 'DESC');
        $qb->setMaxResults(1);
        $query = $qb->getQuery();
        $results = $query->getOneOrNullResult();
        return $results;
    }

    // Retourne la vue suivante
    public function getNextPlanificationViewUFG(\App\Entity\PlanificationPeriod $planificationPeriod, \App\Entity\PlanificationViewUserFileGroup $planificationViewUserFileGroup)
    {
        $qb = $this->createQueryBuilder('pv');
        $qb->where('pv.planificationPeriod = :planificationPeriod')->setParameter('planificationPeriod', $planificationPeriod);
        $qb->andWhere('pv.oorder > :oorder')->setParameter('oorder', $planificationViewUserFileGroup->getOrder());
        $qb->orderBy('pv.oorder', 'ASC');
        $qb->setMaxResults(1);
        $query = $qb->getQuery();
        $results = $query->getOneOrNullResult();
        return $results;
    }

    public function getManualPlanificationViewUFGCount(\App\Entity\PlanificationPeriod $planificationPeriod)
    {
        $qb = $this->createQueryBuilder('pv');
        $qb->select($qb->expr()->count('pv'));
        $qb->where('pv.planificationPeriod = :planificationPeriod')->setParameter('planificationPeriod', $planificationPeriod);
        $qb->andWhere('ufg.type = :type')->setParameter('type', 'MANUAL');
        $qb->innerJoin('pv.userFileGroup', 'ufg');
        $query = $qb->getQuery();
        $singleScalar = $query->getSingleScalarResult();
        return $singleScalar;
    }

    public function getMinManualPlanificationViewUFGOrder(\App\Entity\PlanificationPeriod $planificationPeriod)
    {
        $qb = $this->createQueryBuilder('pv');
        $qb->select($qb->expr()->min('pv.oorder'));
        $qb->where('pv.planificationPeriod = :planificationPeriod')->setParameter('planificationPeriod', $planificationPeriod);
        $qb->andWhere('ufg.type = :type')->setParameter('type', 'MANUAL');
        $qb->innerJoin('pv.userFileGroup', 'ufg');

        $query = $qb->getQuery();
        $singleScalar = $query->getSingleScalarResult();
        if ($singleScalar == null) {
            $singleScalar = 0;
        }
        return $singleScalar;
    }

    public function getMaxManualPlanificationViewUFGOrder(\App\Entity\PlanificationPeriod $planificationPeriod)
    {
        $qb = $this->createQueryBuilder('pv');
        $qb->select($qb->expr()->max('pv.oorder'));
        $qb->where('pv.planificationPeriod = :planificationPeriod')->setParameter('planificationPeriod', $planificationPeriod);
        $qb->andWhere('ufg.type = :type')->setParameter('type', 'MANUAL');
        $qb->innerJoin('pv.userFileGroup', 'ufg');

        $query = $qb->getQuery();
        $singleScalar = $query->getSingleScalarResult();
        if ($singleScalar == null) {
            $singleScalar = 0;
        }
        return $singleScalar;
    }

    public function getMaxPlanificationViewUFGOrder(\App\Entity\PlanificationPeriod $planificationPeriod)
    {
        $qb = $this->createQueryBuilder('pv');
        $qb->select($qb->expr()->max('pv.oorder'));
        $qb->where('pv.planificationPeriod = :planificationPeriod')->setParameter('planificationPeriod', $planificationPeriod);

        $query = $qb->getQuery();
        $singleScalar = $query->getSingleScalarResult();
        return $singleScalar;
    }
}
