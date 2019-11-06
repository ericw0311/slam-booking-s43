<?php
namespace App\Repository;

use App\Entity\Label;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;

/**
 * @method Label|null find($id, $lockMode = null, $lockVersion = null)
 * @method Label|null findOneBy(array $criteria, array $orderBy = null)
 * @method Label[]    findAll()
 * @method Label[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LabelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Label::class);
    }

    public function getLabelsCount(\App\Entity\File $file)
    {
    $qb = $this->createQueryBuilder('l');
    $qb->select($qb->expr()->count('l'));
    $qb->where('l.file = :file')->setParameter('file', $file);
    $query = $qb->getQuery();
    $singleScalar = $query->getSingleScalarResult();
    return $singleScalar;
    }

    public function getLabels(\App\Entity\File $file)
    {
    $qb = $this->createQueryBuilder('l');
    $qb->where('l.file = :file')->setParameter('file', $file);
    $qb->orderBy('l.name', 'ASC');

    $query = $qb->getQuery();
    $results = $query->getResult();
    return $results;
	}

    public function getDisplayedLabels(\App\Entity\File $file, $firstRecordIndex, $maxRecord)
    {
    $qb = $this->createQueryBuilder('l');
    $qb->where('l.file = :file')->setParameter('file', $file);
    $qb->orderBy('l.name', 'ASC');
    $qb->setFirstResult($firstRecordIndex);
    $qb->setMaxResults($maxRecord);

    $query = $qb->getQuery();
    $results = $query->getResult();
    return $results;
	}
}
