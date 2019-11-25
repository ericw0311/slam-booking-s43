<?php
namespace App\Repository;

use App\Entity\UserFileGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;

/**
 * @method UserFileGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserFileGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserFileGroup[]    findAll()
 * @method UserFileGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserFileGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserFileGroup::class);
    }

    public function getUserFileGroupsCount(\App\Entity\File $file)
    {
        $qb = $this->createQueryBuilder('ufg');
        $qb->select($qb->expr()->count('ufg'));
        $qb->where('ufg.file = :file')->setParameter('file', $file);
        $query = $qb->getQuery();
        $singleScalar = $query->getSingleScalarResult();
        return $singleScalar;
    }

    public function getUserFileGroups(\App\Entity\File $file)
    {
        $qb = $this->createQueryBuilder('ufg');
        $qb->where('ufg.file = :file')->setParameter('file', $file);
        $qb->orderBy('ufg.name', 'ASC');

        $query = $qb->getQuery();
        $results = $query->getResult();
        return $results;
    }

    public function getDisplayedUserFileGroups(\App\Entity\File $file, $firstRecordIndex, $maxRecord)
    {
        $qb = $this->createQueryBuilder('ufg');
        $qb->where('ufg.file = :file')->setParameter('file', $file);
        $qb->orderBy('ufg.name', 'ASC');
        $qb->setFirstResult($firstRecordIndex);
        $qb->setMaxResults($maxRecord);

        $query = $qb->getQuery();
        $results = $query->getResult();
        return $results;
    }

    // Retourne les groupes d'utilisateurs non rattachés à une période de planification
    public function getUserFileGroupsToAddToView(\App\Entity\File $file, $userFileGroupsInPlanificationViewUFG_QB)
    {
        $qb = $this->createQueryBuilder('ufg');
        $qb->where('ufg.file = :file')->setParameter('file', $file);

        $qb->andWhere($qb->expr()->not($qb->expr()->exists($userFileGroupsInPlanificationViewUFG_QB->getDQL())));

        $qb->orderBy('ufg.name', 'ASC');
        $query = $qb->getQuery();
        $results = $query->getResult();
        return $results;
    }
}
