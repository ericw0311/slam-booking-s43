<?php
namespace App\Repository;

use App\Entity\Resource;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;

/**
 * @method Resource|null find($id, $lockMode = null, $lockVersion = null)
 * @method Resource|null findOneBy(array $criteria, array $orderBy = null)
 * @method Resource[]    findAll()
 * @method Resource[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResourceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Resource::class);
    }

    public function getResourcesCount(\App\Entity\File $file)
    {
    $qb = $this->createQueryBuilder('r');
    $qb->select($qb->expr()->count('r'));
    $qb->where('r.file = :file')->setParameter('file', $file);
	$qb->andWhere($qb->expr()->not($qb->expr()->eq('r.type', '?1')));
	$qb->setParameter(1, 'USER');
    $query = $qb->getQuery();
    $singleScalar = $query->getSingleScalarResult();
    return $singleScalar;
    }

	public function getResources(\App\Entity\File $file)
    {
    $qb = $this->createQueryBuilder('r');
    $qb->where('r.file = :file')->setParameter('file', $file);
	$qb->andWhere($qb->expr()->not($qb->expr()->eq('r.type', '?1')));
    $qb->orderBy('r.type', 'ASC');
    $qb->addOrderBy('r.internal', 'DESC');
    $qb->addOrderBy('r.code', 'ASC');
    $qb->addOrderBy('r.name', 'ASC');
	$qb->setParameter(1, 'USER');

    $query = $qb->getQuery();
    $results = $query->getResult();
    return $results;
    }

	public function getDisplayedResources(\App\Entity\File $file, $firstRecordIndex, $maxRecord)
    {
    $qb = $this->createQueryBuilder('r');
    $qb->where('r.file = :file')->setParameter('file', $file);
	$qb->andWhere($qb->expr()->not($qb->expr()->eq('r.type', '?1')));
    $qb->orderBy('r.type', 'ASC');
    $qb->addOrderBy('r.internal', 'DESC');
    $qb->addOrderBy('r.code', 'ASC');
    $qb->addOrderBy('r.name', 'ASC');
    $qb->setFirstResult($firstRecordIndex);
    $qb->setMaxResults($maxRecord);
	$qb->setParameter(1, 'USER');

    $query = $qb->getQuery();
    $results = $query->getResult();
    return $results;
    }

	// Compte le nombre de ressources à planifier (tous types): NON UTILISE
	public function getResourcesToPlanifyCount(\App\Entity\File $file, $resourcePlanifiedQB)
    {
    $qb = $this->createQueryBuilder('r');
    $qb->select($qb->expr()->count('r'));
    $qb->where('r.file = :file')->setParameter('file', $file);

	$qb->andWhere($qb->expr()->not($qb->expr()->exists($resourcePlanifiedQB->getDQL())));

	$query = $qb->getQuery();
	$singleScalar = $query->getSingleScalarResult();
	return $singleScalar;
    }

	// Retourne les ressources à planifier (pour un type)
	public function getResourcesToPlanify(\App\Entity\File $file, $type, $resourcePlanifiedQB)
    {
    $qb = $this->createQueryBuilder('r');
    $qb->where('r.file = :file')->setParameter('file', $file);
    $qb->andWhere('r.type = :type')->setParameter('type', $type);

	$qb->andWhere($qb->expr()->not($qb->expr()->exists($resourcePlanifiedQB->getDQL())));

    $qb->orderBy('r.name', 'ASC');
    $query = $qb->getQuery();
    $results = $query->getResult();
    return $results;
    }

	// Retourne les types de ressources à planifier
	public function getResourceTypesToPlanify(\App\Entity\File $file, $resourcePlanifiedQB)
    {
    $qb = $this->createQueryBuilder('r');
    $qb->select('r.type');
    $qb->addSelect($qb->expr()->count('r'));
    $qb->where('r.file = :file')->setParameter('file', $file);

	$qb->andWhere($qb->expr()->not($qb->expr()->exists($resourcePlanifiedQB->getDQL())));

    $qb->groupBy('r.type');
    $qb->orderBy('r.type', 'ASC');
    $query = $qb->getQuery();
    $results = $query->getResult();
    return $results;
    }

    // Retourne le nombre de ressources d'une classification interne
    public function getResourcesCount_IRC(\App\Entity\File $file, $resourceType, $resourceClassificationCode)
    {
    $qb = $this->createQueryBuilder('r');
    $qb->select($qb->expr()->count('r'));
    $qb->where('r.file = :file')->setParameter('file', $file);
    $qb->andWhere('r.type = :type')->setParameter('type', $resourceType);
    $qb->andWhere('r.internal = :internal')->setParameter('internal', 1);
    $qb->andWhere('r.code = :code')->setParameter('code', $resourceClassificationCode);
    $query = $qb->getQuery();
    $singleScalar = $query->getSingleScalarResult();
    return $singleScalar;
    }

    // Retourne les ressources d'une classification interne
    public function getResources_IRC(\App\Entity\File $file, $resourceType, $resourceClassificationCode)
    {
	$qb = $this->createQueryBuilder('r');
    $qb->where('r.file = :file')->setParameter('file', $file);
    $qb->andWhere('r.type = :type')->setParameter('type', $resourceType);
    $qb->andWhere('r.internal = :internal')->setParameter('internal', 1);
    $qb->andWhere('r.code = :code')->setParameter('code', $resourceClassificationCode);
	$qb->orderBy('r.name', 'ASC');
	$query = $qb->getQuery();
	$results = $query->getResult();
	return $results;
    }

    // Retourne le nombre de ressources d'une classification externe
    public function getResourcesCount_ERC(\App\Entity\File $file, $resourceType, \App\Entity\ResourceClassification $resourceClassification)
    {
    $qb = $this->createQueryBuilder('r');
    $qb->select($qb->expr()->count('r'));
    $qb->where('r.file = :file')->setParameter('file', $file);
    $qb->andWhere('r.type = :type')->setParameter('type', $resourceType);
    $qb->andWhere('r.internal = :internal')->setParameter('internal', 0);
    $qb->andWhere('r.classification = :classification')->setParameter('classification', $resourceClassification);
    $query = $qb->getQuery();
    $singleScalar = $query->getSingleScalarResult();
    return $singleScalar;
    }

    // Retourne les ressources d'une classification externe
    public function getResources_ERC(\App\Entity\File $file, $resourceType, \App\Entity\ResourceClassification $resourceClassification)
    {
	$qb = $this->createQueryBuilder('r');
    $qb->where('r.file = :file')->setParameter('file', $file);
    $qb->andWhere('r.type = :type')->setParameter('type', $resourceType);
    $qb->andWhere('r.internal = :internal')->setParameter('internal', 0);
    $qb->andWhere('r.classification = :classification')->setParameter('classification', $resourceClassification);
	$qb->orderBy('r.name', 'ASC');
	$query = $qb->getQuery();
	$results = $query->getResult();
	return $results;
    }
}
