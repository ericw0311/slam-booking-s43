<?php
namespace App\Repository;

use App\Entity\Timetable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;

/**
 * @method Timetable|null find($id, $lockMode = null, $lockVersion = null)
 * @method Timetable|null findOneBy(array $criteria, array $orderBy = null)
 * @method Timetable[]    findAll()
 * @method Timetable[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimetableRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Timetable::class);
    }

    public function getTimetablesCount($file)
      {
      $qb = $this->createQueryBuilder('t');
      $qb->select($qb->expr()->count('t'));
      $qb->where('t.file = :file')->setParameter('file', $file);
      $query = $qb->getQuery();
      $singleScalar = $query->getSingleScalarResult();
      return $singleScalar;
      }

      public function getDisplayedTimetables($file, $firstRecordIndex, $maxRecord)
      {
      $qb = $this->createQueryBuilder('t');
      $qb->where('t.file = :file')->setParameter('file', $file);
      $qb->orderBy('t.type', 'ASC');
      $qb->addOrderBy('t.name', 'ASC');
      $qb->setFirstResult($firstRecordIndex);
      $qb->setMaxResults($maxRecord);

      $query = $qb->getQuery();
      $results = $query->getResult();
      return $results;
      }

  	// Les grilles horaires du dossier. Query builder uniquement: utilisé pour le formulaire des lignes de planification.
      public function getTimetablesQB($file)
      {
      $qb = $this->createQueryBuilder('t');
  	$qb->where('t.file = :file')->setParameter('file', $file);
      $qb->orderBy('t.name', 'ASC');

      return $qb;
      }

  	// Retourne la premiere grille horaire
  	public function getFirstTimetable($file)
      {
      $qb = $this->createQueryBuilder('t');
  	$qb->where('t.file = :file')->setParameter('file', $file);
      $qb->orderBy('t.name', 'ASC');
  	$qb->setMaxResults(1);
  	$query = $qb->getQuery();
  	$results = $query->getOneOrNullResult();
  	return $results;
  	}

  	// Nombre de grilles horaires saisies par l'utilisateur (type = T)
      public function getUserTimetablesCount($file)
      {
      $qb = $this->createQueryBuilder('t');
      $qb->select($qb->expr()->count('t'));
      $qb->where('t.file = :file')->setParameter('file', $file);
      $qb->andWhere('t.type = :type')->setParameter('type', 'T');
      $query = $qb->getQuery();
      $singleScalar = $query->getSingleScalarResult();
      return $singleScalar;
      }

  	// Liste des grilles horaires saisies par l'utilisateur (type = T)
      public function getUserTimetables($file)
      {
      $qb = $this->createQueryBuilder('t');
      $qb->where('t.file = :file')->setParameter('file', $file);
      $qb->andWhere('t.type = :type')->setParameter('type', 'T');
      $qb->orderBy('t.type', 'ASC');
      $qb->addOrderBy('t.name', 'ASC');

      $query = $qb->getQuery();
      $results = $query->getResult();
      return $results;
      }
}
