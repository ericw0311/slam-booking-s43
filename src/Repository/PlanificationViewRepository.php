<?php
namespace App\Repository;

use App\Entity\PlanificationView;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;

/**
 * @method PlanificationView|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanificationView|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanificationView[]    findAll()
 * @method PlanificationView[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanificationViewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanificationView::class);
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
    public function getUserFileGroupsInPlanificationViewQB(\App\Entity\PlanificationPeriod $planificationPeriod)
    {
        $qb = $this->createQueryBuilder('pv');
        $qb->where('pv.userFileGroup = ufg.id and pv.planificationPeriod = '.$planificationPeriod->getID());
        return $qb;
    }

    // Retourne la premiere vue
    public function getFirstPlanificationView(\App\Entity\PlanificationPeriod $planificationPeriod)
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
    public function getPreviousPlanificationView(\App\Entity\PlanificationPeriod $planificationPeriod, \App\Entity\PlanificationView $planificationView)
    {
        $qb = $this->createQueryBuilder('pv');
        $qb->where('pv.planificationPeriod = :planificationPeriod')->setParameter('planificationPeriod', $planificationPeriod);
        $qb->andWhere('pv.oorder < :oorder')->setParameter('oorder', $planificationView->getOrder());
        $qb->orderBy('pv.oorder', 'DESC');
        $qb->setMaxResults(1);
        $query = $qb->getQuery();
        $results = $query->getOneOrNullResult();
        return $results;
    }

    // Retourne la vue suivante
    public function getNextPlanificationView(\App\Entity\PlanificationPeriod $planificationPeriod, \App\Entity\PlanificationView $planificationView)
    {
        $qb = $this->createQueryBuilder('pv');
        $qb->where('pv.planificationPeriod = :planificationPeriod')->setParameter('planificationPeriod', $planificationPeriod);
        $qb->andWhere('pv.oorder > :oorder')->setParameter('oorder', $planificationView->getOrder());
        $qb->orderBy('pv.oorder', 'ASC');
        $qb->setMaxResults(1);
        $query = $qb->getQuery();
        $results = $query->getOneOrNullResult();
        return $results;
    }

    public function getManualPlanificationViewCount(\App\Entity\PlanificationPeriod $planificationPeriod)
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

    public function getMinManualPlanificationViewOrder(\App\Entity\PlanificationPeriod $planificationPeriod)
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

    public function getMaxManualPlanificationViewOrder(\App\Entity\PlanificationPeriod $planificationPeriod)
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

    public function getMaxPlanificationViewOrder(\App\Entity\PlanificationPeriod $planificationPeriod)
    {
        $qb = $this->createQueryBuilder('pv');
        $qb->select($qb->expr()->max('pv.oorder'));
        $qb->where('pv.planificationPeriod = :planificationPeriod')->setParameter('planificationPeriod', $planificationPeriod);

        $query = $qb->getQuery();
        $singleScalar = $query->getSingleScalarResult();
        return $singleScalar;
    }
}
