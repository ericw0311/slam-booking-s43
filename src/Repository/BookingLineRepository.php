<?php
namespace App\Repository;

use App\Entity\BookingLine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;

/**
 * @method BookingLine|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookingLine|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookingLine[]    findAll()
 * @method BookingLine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingLineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookingLine::class);
    }

    public function getBookingLines(\App\Entity\Booking $booking)
    {
        $qb = $this->createQueryBuilder('bl');
        $qb->select('bl.ddate date');
        $qb->addSelect('p.id planificationID');
        $qb->addSelect('pp.id planificationPeriodID');
        $qb->addSelect('pl.id planificationLineID');
        $qb->addSelect('t.id timetableID');
        $qb->addSelect('tl.id timetableLineID');
        $qb->where('bl.booking = :booking')->setParameter('booking', $booking);
        $qb->innerJoin('bl.planification', 'p');
        $qb->innerJoin('bl.planificationPeriod', 'pp');
        $qb->innerJoin('bl.planificationLine', 'pl');
        $qb->innerJoin('bl.timetable', 't');
        $qb->innerJoin('bl.timetableLine', 'tl');
        $qb->orderBy('bl.ddate', 'ASC');
        $qb->addOrderBy('p.id', 'ASC');
        $qb->addOrderBy('pp.id', 'ASC');
        $qb->addOrderBy('pl.id', 'ASC');
        $qb->addOrderBy('t.id', 'ASC');
        $qb->addOrderBy('tl.id', 'ASC');
        $query = $qb->getQuery();
        $results = $query->getResult();
        return $results;
    }

    // Date maximum parmi les réservations d'une planification
    public function getLastPlanificationBookingLine(\App\Entity\File $file, \App\Entity\Planification $planification)
    {
        $qb = $this->createQueryBuilder('bl');
        $qb->where('bl.planification = :p')->setParameter('p', $planification);
        $qb->innerJoin('bl.booking', 'b', Expr\Join::WITH, $qb->expr()->eq('b.file', '?1'));
        $qb->setParameter(1, $file);
        $qb->orderBy('bl.ddate', 'DESC');
        $qb->setMaxResults(1);

        $query = $qb->getQuery();
        $results = $query->getOneOrNullResult();
        return $results;
    }

    // Première ligne d'une réservation
    public function getFirstBookingLine(\App\Entity\Booking $booking)
    {
        $qb = $this->createQueryBuilder('bl');
        $qb->where('bl.booking = :b')->setParameter('b', $booking);
        $qb->orderBy('bl.id', 'ASC');
        $qb->setMaxResults(1);

        $query = $qb->getQuery();
        $results = $query->getOneOrNullResult();
        return $results;
    }

    // Dernière ligne d'une réservation
    public function getLastBookingLine(\App\Entity\Booking $booking)
    {
        $qb = $this->createQueryBuilder('bl');
        $qb->where('bl.booking = :b')->setParameter('b', $booking);
        $qb->orderBy('bl.id', 'DESC');
        $qb->setMaxResults(1);

        $query = $qb->getQuery();
        $results = $query->getOneOrNullResult();
        return $results;
    }

    // Construit le Query Builder d'une Ligne de réservation référençant une grille horaire
    public function getTimetableBookingLineQB()
    {
        $qb = $this->createQueryBuilder('bl');
        $qb->where('bl.booking = b.id');
        $qb->andWhere('bl.timetable = :timetable');
        return $qb;
    }

    // Construit le Query Builder d'une Ligne de réservation référençant une planification
    public function getPlanificationBookingLineQB()
    {
        $qb = $this->createQueryBuilder('bl');
        $qb->where('bl.booking = b.id');
        $qb->andWhere('bl.planification = :planification');
        return $qb;
    }

    // Construit le Query Builder d'une Ligne de réservation référençant une planification
    public function getPlanificationPeriodBookingLineQB()
    {
        $qb = $this->createQueryBuilder('bl');
        $qb->where('bl.booking = b.id');
        $qb->andWhere('bl.planification = :planification');
        $qb->andWhere('bl.planificationPeriod = :planificationPeriod');
        return $qb;
    }

    // Construit le Query Builder d'acces aux ressources pour un utilisateur dossier
    public function getResourceUserFileQB(\App\Entity\UserFile $userFile)
    {
        $qb = $this->createQueryBuilder('bl');
        $qb->where('bl.booking = b.id');
        $qb->innerJoin('bl.planificationPeriod', 'pp');
        $qb->innerJoin('pp.planificationViewUserFileGroups', 'pvufg', Expr\Join::WITH, 'pvufg.active = 1');
        $qb->innerJoin('pvufg.userFileGroup', 'ufg');
        $qb->innerJoin('ufg.userFiles', 'uf2', Expr\Join::WITH, 'uf2.id = '.$userFile->getId());
        $qb->innerJoin('pvufg.planificationViewResources', 'pvr', Expr\Join::WITH, 'pvr.active = 1');
        $qb->innerJoin('pvr.planificationResource', 'pr');
        $qb->andWhere('pr.resource = r.id');
        return $qb;
    }
}
