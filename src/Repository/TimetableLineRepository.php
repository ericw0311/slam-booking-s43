<?php
namespace App\Repository;

use App\Entity\TimetableLine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;

/**
 * @method TimetableLine|null find($id, $lockMode = null, $lockVersion = null)
 * @method TimetableLine|null findOneBy(array $criteria, array $orderBy = null)
 * @method TimetableLine[]    findAll()
 * @method TimetableLine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimetableLineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TimetableLine::class);
    }

    // Recherche les creneaux d'une grille horaire
      public function getTimetableLines($timetable)
      {
      $qb = $this->createQueryBuilder('tl');
      $qb->where('tl.timetable = :timetable')->setParameter('timetable', $timetable);
      $qb->orderBy('tl.id', 'ASC');

      $query = $qb->getQuery();
      $results = $query->getResult();
      return $results;
      }

      // Recherche les derniers creneaux horaires d'une grille
      public function getLastTimetableLines($timetable, $maxRecord)
      {
      $qb = $this->createQueryBuilder('tl');
      $qb->where('tl.timetable = :timetable')->setParameter('timetable', $timetable);
      $qb->orderBy('tl.id', 'DESC');
      $qb->setMaxResults($maxRecord);

      $query = $qb->getQuery();
      $results = $query->getResult();
      return $results;
      }

      // Recherche quelques creneaux horaires d'une grille a partir d'une position
      public function getSomeTimetableLines($timetable, $timetableLineID, $maxRecord, $previous = false)
      {
      $qb = $this->createQueryBuilder('tl');
      $qb->where('tl.timetable = :timetable')->setParameter('timetable', $timetable);
      if ($previous) {
          $qb->andWhere('tl.id < :timetableLineID')->setParameter('timetableLineID', $timetableLineID);
      } else {
          $qb->andWhere('tl.id > :timetableLineID')->setParameter('timetableLineID', $timetableLineID);
      }
      if ($previous) {
          $qb->orderBy('tl.id', 'DESC');
      } else {
          $qb->orderBy('tl.id', 'ASC');
      }
      $qb->setMaxResults($maxRecord);

      $query = $qb->getQuery();
      $results = $query->getResult();
      return $results;
      }

  	// Retourne le creneau horaire precedent
  	public function getPreviousTimetableLine($timetable, $timetableLineID)
      {
  	$qb = $this->createQueryBuilder('tl');
  	$qb->where('tl.timetable = :timetable')->setParameter('timetable', $timetable);
  	$qb->andWhere('tl.id < :timetableLineID')->setParameter('timetableLineID', $timetableLineID);
  	$qb->orderBy('tl.id', 'DESC');
  	$qb->setMaxResults(1);
  	$query = $qb->getQuery();
  	$results = $query->getOneOrNullResult();
  	return $results;
  	}

  	// Retourne le creneau horaire suivant
  	public function getNextTimetableLine($timetable, $timetableLineID)
      {
  	$qb = $this->createQueryBuilder('tl');
  	$qb->where('tl.timetable = :timetable')->setParameter('timetable', $timetable);
  	$qb->andWhere('tl.id > :timetableLineID')->setParameter('timetableLineID', $timetableLineID);
  	$qb->orderBy('tl.id', 'ASC');
  	$qb->setMaxResults(1);
  	$query = $qb->getQuery();
  	$results = $query->getOneOrNullResult();
  	return $results;
  	}

  	// Retourne le creneau horaire courant et les suivants
  	public function getCurrentAndNextTimetableLines($timetable, $timetableLineID)
      {
  	$qb = $this->createQueryBuilder('tl');
  	$qb->where('tl.timetable = :timetable')->setParameter('timetable', $timetable);
  	$qb->andWhere('tl.id >= :timetableLineID')->setParameter('timetableLineID', $timetableLineID);
  	$qb->orderBy('tl.id', 'ASC');
  	$query = $qb->getQuery();
      $results = $query->getResult();
  	return $results;
  	}

  	// Retourne le dernier creneau horaire
  	public function getLastTimetableLine($timetable)
  	{
  	$qb = $this->createQueryBuilder('tl');
  	$qb->where('tl.timetable = :timetable')->setParameter('timetable', $timetable);
  	$qb->orderBy('tl.id', 'DESC');
  	$qb->setMaxResults(1);
  	$query = $qb->getQuery();
  	$results = $query->getOneOrNullResult();
  	return $results;
  	}
}
