<?php
namespace App\Repository;

use App\Entity\ResourceClassification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;

/**
 * @method ResourceClassification|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResourceClassification|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResourceClassification[]    findAll()
 * @method ResourceClassification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResourceClassificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResourceClassification::class);
    }

    public function getInternalResourceClassificationCodes($file, $resourceType, $active)
      {
      $qb = $this->createQueryBuilder('rc');
      $qb->where('rc.file = :file')->setParameter('file', $file);
      $qb->andWhere('rc.type = :type')->setParameter('type', $resourceType);
      $qb->andWhere('rc.internal = :internal')->setParameter('internal', 1);
      $qb->andWhere('rc.active = :active')->setParameter('active', $active);

      $query = $qb->getQuery();
      $results = $query->getResult();
  	// On retourne un tableau des codes classifications sélectionnés
  	$resourceClassificationCodes = array();
      foreach ($results as $resourceClassification) {
  		array_push($resourceClassificationCodes, $resourceClassification->getCode());
  	}
      return $resourceClassificationCodes;
      }

      public function getExternalResourceClassifications($file, $resourceType)
      {
      $qb = $this->createQueryBuilder('rc');
      $qb->where('rc.file = :file')->setParameter('file', $file);
      $qb->andWhere('rc.type = :type')->setParameter('type', $resourceType);
      $qb->andWhere('rc.internal = :internal')->setParameter('internal', 0);
      $qb->orderBy('rc.name', 'ASC');
      $query = $qb->getQuery();
      $results = $query->getResult();
      return $results;
      }

      public function getActiveExternalResourceClassifications($file, $resourceType)
      {
      $qb = $this->createQueryBuilder('rc');
      $qb->where('rc.file = :file')->setParameter('file', $file);
      $qb->andWhere('rc.type = :type')->setParameter('type', $resourceType);
      $qb->andWhere('rc.internal = :internal')->setParameter('internal', 0);
      $qb->andWhere('rc.active = :active')->setParameter('active', 1);
      $qb->orderBy('rc.name', 'ASC');
      $query = $qb->getQuery();
      $results = $query->getResult();
      return $results;
      }

  	// Retourne la première classification de ressource externe active
  	public function getFirsrActiveExternalResourceClassification($file, $resourceType)
      {
      $qb = $this->createQueryBuilder('rc');
      $qb->where('rc.file = :file')->setParameter('file', $file);
      $qb->andWhere('rc.type = :type')->setParameter('type', $resourceType);
      $qb->andWhere('rc.internal = :internal')->setParameter('internal', 0);
      $qb->andWhere('rc.active = :active')->setParameter('active', 1);
      $qb->orderBy('rc.name', 'ASC');
  	$qb->setMaxResults(1);
      $query = $qb->getQuery();
  	$results = $query->getOneOrNullResult();
      return $results;
      }
}
