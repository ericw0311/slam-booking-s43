<?php
namespace App\Repository;

use App\Entity\Booking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;

use App\Api\DateFormatFunctionDQL;

/**
 * @method Booking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Booking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Booking[]    findAll()
 * @method Booking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    // Affichage des réservations dans le planning
    public function getPlanningBookings(\App\Entity\File $file, \Datetime $beginningDate, \Datetime $endDate, \App\Entity\Planification $planification, \App\Entity\PlanificationPeriod $planificationPeriod)
    {
        $qb = $this->createQueryBuilder('b');
        $this->getPlanningSelect($qb);
        $qb->where('b.file = :file')->setParameter('file', $file);
        $qb->andWhere($qb->expr()->between("DATE_FORMAT(bl.ddate,'%Y%m%d')", ':beginningDate', ':endDate'))
        ->setParameter('beginningDate', $beginningDate->format('Ymd'))
        ->setParameter('endDate', $endDate->format('Ymd'));
        $qb->andWhere('bl.planification = :planification')->setParameter('planification', $planification);
        $qb->andWhere('bl.planificationPeriod = :planificationPeriod')->setParameter('planificationPeriod', $planificationPeriod);
        $this->getPlanningJoin($qb);
        $this->getPlanningOrder($qb);
        $query = $qb->getQuery();
        $results = $query->getResult();
        return $results;
    }

    // Affichage des réservations pour la duplication des réservations (on traite deux périodes et une seule ressource)
    public function getDuplicateBookings(
        \App\Entity\File $file,
        \Datetime $beginningDate,
        \Datetime $endDate,
        \Datetime $newBookingBeginningDate,
        \Datetime $newBookingEndDate,
        \App\Entity\Planification $planification,
        \App\Entity\PlanificationPeriod $planificationPeriod,
        \App\Entity\Resource $resource
    ) {
        $qb = $this->createQueryBuilder('b');
        $this->getPlanningSelect($qb);
        $qb->where('b.file = :file')->setParameter('file', $file);
        $qb->andWhere($qb->expr()->orX($qb->expr()->between("DATE_FORMAT(bl.ddate,'%Y%m%d')", ':beginningDate', ':endDate'), $qb->expr()->between("DATE_FORMAT(bl.ddate,'%Y%m%d')", ':newBookingBeginningDate', ':newBookingEndDate')))
        ->setParameter('beginningDate', $beginningDate->format('Ymd'))
        ->setParameter('endDate', $endDate->format('Ymd'))
        ->setParameter('newBookingBeginningDate', $newBookingBeginningDate->format('Ymd'))
        ->setParameter('newBookingEndDate', $newBookingEndDate->format('Ymd'));
        $qb->andWhere('bl.planification = :planification')->setParameter('planification', $planification);
        $qb->andWhere('bl.planificationPeriod = :planificationPeriod')->setParameter('planificationPeriod', $planificationPeriod);
        $qb->andWhere('bl.resource = :resource')->setParameter('resource', $resource);
        $this->getPlanningJoin($qb);
        $this->getPlanningOrder($qb);
        $query = $qb->getQuery();
        $results = $query->getResult();
        return $results;
    }

    // Planning des réservations: partie Select
    public function getPlanningSelect($qb)
    {
        $qb->select('b.id bookingID');
        $qb->addSelect('bl.ddate date');
        $qb->addSelect('p.id planificationID');
        $qb->addSelect('pp.id planificationPeriodID');
        $qb->addSelect('pl.id planificationLineID');
        $qb->addSelect('r.id resourceID');
        $qb->addSelect('t.id timetableID');
        $qb->addSelect('tl.id timetableLineID');
    }


    // Planning des réservations: partie Join
    public function getPlanningJoin($qb)
    {
        $qb->innerJoin('b.bookingLines', 'bl');
        $qb->innerJoin('bl.planification', 'p');
        $qb->innerJoin('bl.planificationPeriod', 'pp');
        $qb->innerJoin('bl.planificationLine', 'pl');
        $qb->innerJoin('bl.resource', 'r');
        $qb->innerJoin('bl.timetable', 't');
        $qb->innerJoin('bl.timetableLine', 'tl');
    }

    // Planning des réservations: partie Order
    public function getPlanningOrder($qb)
    {
        $qb->orderBy('bl.ddate', 'ASC');
        $qb->addOrderBy('p.id', 'ASC');
        $qb->addOrderBy('pp.id', 'ASC');
        $qb->addOrderBy('pl.id', 'ASC');
        $qb->addOrderBy('r.id', 'ASC');
        $qb->addOrderBy('t.id', 'ASC');
        $qb->addOrderBy('tl.id', 'ASC');
    }

    // Toutes les réservations d'un dossier
    public function getAllBookingsCount(\App\Entity\File $file)
    {
        $qb = $this->createQueryBuilder('b');
        $qb->select($qb->expr()->count('b'));
        $qb->where('b.file = :file')->setParameter('file', $file);
        $query = $qb->getQuery();
        $singleScalar = $query->getSingleScalarResult();
        return $singleScalar;
    }

    public function getAllBookings(\App\Entity\File $file, $firstRecordIndex, $maxRecord)
    {
        $qb = $this->createQueryBuilder('b');
        $this->getListSelect($qb);
        $qb->where('b.file = :file')->setParameter('file', $file);
        $this->getListJoin_1($qb);
        $this->getListSort($qb);
        $qb->setFirstResult($firstRecordIndex);
        $qb->setMaxResults($maxRecord);
        $query = $qb->getQuery();
        $results = $query->getResult();
        return $results;
    }

    // Les réservations d'un dossier au delà d'une date
    public function getFromDatetimeBookingsCount(\App\Entity\File $file, \Datetime $dateTime)
    {
        $qb = $this->createQueryBuilder('b');
        $qb->select($qb->expr()->count('b'));
        $qb->where('b.file = :file')->setParameter('file', $file);
        $qb->andWhere("DATE_FORMAT(b.endDate,'%Y%m%d%H%i') >= :dateTime")->setParameter('dateTime', $dateTime->format('YmdHi'));
        $query = $qb->getQuery();
        $singleScalar = $query->getSingleScalarResult();
        return $singleScalar;
    }

    public function getFromDatetimeBookings(\App\Entity\File $file, \Datetime $dateTime, $firstRecordIndex, $maxRecord)
    {
        $qb = $this->createQueryBuilder('b');
        $this->getListSelect($qb);
        $qb->where('b.file = :file')->setParameter('file', $file);
        $qb->andWhere("DATE_FORMAT(b.endDate,'%Y%m%d%H%i') >= :dateTime")->setParameter('dateTime', $dateTime->format('YmdHi'));
        $this->getListJoin_1($qb);
        $this->getListSort($qb);
        $qb->setFirstResult($firstRecordIndex);
        $qb->setMaxResults($maxRecord);
        $query = $qb->getQuery();
        $results = $query->getResult();
        return $results;
    }

    // Les réservations d'un dossier et d'un utilisateur
    public function getUserFileBookingsCount(\App\Entity\File $file, \App\Entity\UserFile $userFile)
    {
        $qb = $this->createQueryBuilder('b');
        $qb->select($qb->expr()->count('b'));
        $qb->where('b.file = :file')->setParameter('file', $file);
        $this->getUserFileJoin($qb, $userFile);
        $query = $qb->getQuery();
        $singleScalar = $query->getSingleScalarResult();
        return $singleScalar;
    }

    public function getUserFileBookings(\App\Entity\File $file, \App\Entity\UserFile $userFile, $firstRecordIndex, $maxRecord)
    {
        $qb = $this->createQueryBuilder('b');
        $this->getListSelect($qb);
        $qb->where('b.file = :file')->setParameter('file', $file);
        $this->getListJoin_2($qb, $userFile);
        $this->getListSort($qb);
        $qb->setFirstResult($firstRecordIndex);
        $qb->setMaxResults($maxRecord);
        $query = $qb->getQuery();
        $results = $query->getResult();
        return $results;
    }

    // Les réservations d'un dossier et d'un utilisateur au delà d'une date
    public function getUserFileFromDatetimeBookingsCount(\App\Entity\File $file, \App\Entity\UserFile $userFile, \Datetime $dateTime)
    {
        $qb = $this->createQueryBuilder('b');
        $qb->select($qb->expr()->count('b'));
        $qb->where('b.file = :file')->setParameter('file', $file);
        $qb->andWhere("DATE_FORMAT(b.endDate,'%Y%m%d%H%i') >= :dateTime")->setParameter('dateTime', $dateTime->format('YmdHi'));
        $this->getUserFileJoin($qb, $userFile);
        $query = $qb->getQuery();
        $singleScalar = $query->getSingleScalarResult();
        return $singleScalar;
    }

    public function getUserFileFromDatetimeBookings(\App\Entity\File $file, \App\Entity\UserFile $userFile, \Datetime $dateTime, $firstRecordIndex, $maxRecord)
    {
        $qb = $this->createQueryBuilder('b');
        $this->getListSelect($qb);
        $qb->where('b.file = :file')->setParameter('file', $file);
        $qb->andWhere("DATE_FORMAT(b.endDate,'%Y%m%d%H%i') >= :dateTime")->setParameter('dateTime', $dateTime->format('YmdHi'));
        $this->getListJoin_2($qb, $userFile);
        $this->getListSort($qb);
        $qb->setFirstResult($firstRecordIndex);
        $qb->setMaxResults($maxRecord);
        $query = $qb->getQuery();
        $results = $query->getResult();
        return $results;
    }

    // Les réservations d'un dossier et d'une ressource
    public function getResourceBookingsCount(\App\Entity\File $file, \App\Entity\Resource $resource)
    {
        $qb = $this->createQueryBuilder('b');
        $qb->select($qb->expr()->count('b'));
        $qb->where('b.file = :file')->setParameter('file', $file);
        $qb->andWhere('b.resource = :resource')->setParameter('resource', $resource);
        $query = $qb->getQuery();
        $singleScalar = $query->getSingleScalarResult();
        return $singleScalar;
    }

    public function getResourceBookings(\App\Entity\File $file, \App\Entity\Resource $resource, $firstRecordIndex, $maxRecord)
    {
        $qb = $this->createQueryBuilder('b');
        $this->getListSelect($qb);
        $qb->where('b.file = :file')->setParameter('file', $file);
        $qb->andWhere('b.resource = :resource')->setParameter('resource', $resource);
        $this->getListJoin_1($qb);
        $this->getListSort($qb);
        $qb->setFirstResult($firstRecordIndex);
        $qb->setMaxResults($maxRecord);
        $query = $qb->getQuery();
        $results = $query->getResult();
        return $results;
    }

    public function getTimetableBookingsCount(\App\Entity\File $file, \App\Entity\Timetable $timetable, $timetableBookingLineQB)
    {
        $qb = $this->createQueryBuilder('b');
        $qb->select($qb->expr()->count('b'));
        $qb->where('b.file = :file')->setParameter('file', $file);
        $qb->andWhere($qb->expr()->exists($timetableBookingLineQB->getDQL()))->setParameter('timetable', $timetable);

        $query = $qb->getQuery();
        $singleScalar = $query->getSingleScalarResult();
        return $singleScalar;
    }

    public function getTimetableBookings(\App\Entity\File $file, \App\Entity\Timetable $timetable, $timetableBookingLineQB, $firstRecordIndex, $maxRecord)
    {
        $qb = $this->createQueryBuilder('b');
        $this->getListSelect($qb);
        $qb->where('b.file = :file')->setParameter('file', $file);
        $qb->andWhere($qb->expr()->exists($timetableBookingLineQB->getDQL()))->setParameter('timetable', $timetable);
        $this->getListJoin_1($qb);
        $this->getListSort($qb);
        $qb->setFirstResult($firstRecordIndex);
        $qb->setMaxResults($maxRecord);
        $query = $qb->getQuery();
        $results = $query->getResult();
        return $results;
    }

    // Les réservations d'un dossier et d'une planification
    public function getPlanificationBookingsCount(\App\Entity\File $file, \App\Entity\Planification $planification, $planificationBookingLineQB)
    {
        $qb = $this->createQueryBuilder('b');
        $qb->select($qb->expr()->count('b'));
        $qb->where('b.file = :file')->setParameter('file', $file);
        $qb->andWhere($qb->expr()->exists($planificationBookingLineQB->getDQL()))->setParameter('planification', $planification);
        $query = $qb->getQuery();
        $singleScalar = $query->getSingleScalarResult();
        return $singleScalar;
    }

    // Les réservations d'un dossier et d'une période de planification
    public function getPlanificationPeriodBookingsCount(\App\Entity\File $file, \App\Entity\Planification $planification, \App\Entity\PlanificationPeriod $planificationPeriod, $planificationPeriodBookingLineQB)
    {
        $qb = $this->createQueryBuilder('b');
        $qb->select($qb->expr()->count('b'));
        $qb->where('b.file = :file')->setParameter('file', $file);
        $qb->andWhere($qb->expr()->exists($planificationPeriodBookingLineQB->getDQL()))->setParameter('planification', $planification)->setParameter('planificationPeriod', $planificationPeriod);
        $query = $qb->getQuery();
        $singleScalar = $query->getSingleScalarResult();
        return $singleScalar;
    }

    public function getPlanificationPeriodBookings(\App\Entity\File $file, \App\Entity\Planification $planification, \App\Entity\PlanificationPeriod $planificationPeriod, $planificationPeriodBookingLineQB, $firstRecordIndex, $maxRecord)
    {
        $qb = $this->createQueryBuilder('b');
        $this->getListSelect($qb);
        $qb->where('b.file = :file')->setParameter('file', $file);
        $qb->andWhere($qb->expr()->exists($planificationPeriodBookingLineQB->getDQL()))->setParameter('planification', $planification)->setParameter('planificationPeriod', $planificationPeriod);
        $this->getListJoin_1($qb);
        $this->getListSort($qb);
        $qb->setFirstResult($firstRecordIndex);
        $qb->setMaxResults($maxRecord);
        $query = $qb->getQuery();
        $results = $query->getResult();
        return $results;
    }

    // Les réservations d'un dossier et d'une étiquette
    public function getLabelBookingsCount(\App\Entity\File $file, \App\Entity\Label $label)
    {
        $qb = $this->createQueryBuilder('b');
        $qb->select($qb->expr()->count('b'));
        $qb->where('b.file = :file')->setParameter('file', $file);
        $this->getLabelJoin($qb, $label);
        $query = $qb->getQuery();
        $singleScalar = $query->getSingleScalarResult();
        return $singleScalar;
    }

    public function getLabelBookings(\App\Entity\File $file, \App\Entity\Label $label, $firstRecordIndex, $maxRecord)
    {
        $qb = $this->createQueryBuilder('b');
        $this->getListSelect($qb);
        $qb->where('b.file = :file')->setParameter('file', $file);
        $this->getLabelJoin($qb, $label);
        $this->getListJoin_1($qb);
        $this->getListSort($qb);
        $qb->setFirstResult($firstRecordIndex);
        $qb->setMaxResults($maxRecord);
        $query = $qb->getQuery();
        $results = $query->getResult();
        return $results;
    }

    // Listes de réservations: partie Select
    public function getListSelect($qb)
    {
        $qb->select('b.id');
        $qb->addSelect('b.beginningDate');
        $qb->addSelect('b.endDate');
        $qb->addSelect('p.id planificationID');
        $qb->addSelect('r.name resource_name');
        $qb->addSelect('r.code resource_code');
        $qb->addSelect('r.type resource_type');
        $qb->addSelect('r.internal resource_internal');
        $qb->addSelect('uf.firstName user_first_name');
        $qb->addSelect('uf.lastName user_last_name');
        $qb->addSelect('uf.administrator administrator');
        $qb->addSelect('uf.uniqueName unique_name');
        $qb->addSelect('uf.userCreated user_created');
        $qb->addSelect('uf.userName user_name');
    }

    // Listes de réservations: partie Jointure avec sélection de l'utilisateur d'ordre 1
    public function getListJoin_1($qb)
    {
        $qb->innerJoin('b.planification', 'p');
        $qb->innerJoin('b.resource', 'r');
        $qb->innerJoin('b.bookingUsers', 'bu', Expr\Join::WITH, $qb->expr()->eq('bu.oorder', ':order'))->setParameter('order', 1);
        $qb->innerJoin('bu.userFile', 'uf');
    }

    // Jointure pour sélection de l'utilisateur transmis
    public function getUserFileJoin($qb, \App\Entity\UserFile $userFile)
    {
        $qb->innerJoin('b.bookingUsers', 'bu', Expr\Join::WITH, $qb->expr()->eq('bu.userFile', ':userFile'))->setParameter('userFile', $userFile);
    }

    // Listes de réservations: partie Jointure avec sélection de l'utilisateur transmis
    public function getListJoin_2($qb, \App\Entity\UserFile $userFile)
    {
        $qb->innerJoin('b.planification', 'p');
        $qb->innerJoin('b.resource', 'r');
        $this->getUserFileJoin($qb, $userFile);
        $qb->innerJoin('bu.userFile', 'uf');
    }

    // Jointure pour sélection d'une grille horaire (Plus utilisée car elle sélectionne une réservation autant de fois qu'elle a de lignes, je l'ai remplacée par un exists: BookingLineRepository->getTimetableBookingLineQB)
    public function getTimetableJoin($qb, \App\Entity\Timetable $timetable)
    {
        $qb->innerJoin('b.bookingLines', 'bli', Expr\Join::WITH, $qb->expr()->eq('bli.timetable', ':t'));
        $qb->setParameter('t', $timetable);
    }

    // Jointure pour sélection d'une planification
    public function getPlanificationJoin($qb, \App\Entity\Planification $planification)
    {
        $qb->innerJoin('b.bookingLines', 'bli', Expr\Join::WITH, $qb->expr()->eq('bli.planification', ':p'));
        $qb->setParameter('p', $planification);
    }

    // Jointure pour sélection d'une période de planification
    public function getPlanificationPeriodJoin($qb, \App\Entity\Planification $planification, \App\Entity\PlanificationPeriod $planificationPeriod)
    {
        $qb->innerJoin(
            'b.bookingLines',
            'bli',
            Expr\Join::WITH,
            $qb->expr()->andX(
                $qb->expr()->eq('bli.planification', ':p'),
                $qb->expr()->eq('bli.planificationPeriod', ':pp')
        )
    );
        $qb->setParameter('p', $planification);
        $qb->setParameter('pp', $planificationPeriod);
    }

    // Jointure pour sélection d'une étiquete
    public function getLabelJoin($qb, \App\Entity\Label $label)
    {
        $qb->innerJoin('b.bookingLabels', 'bla', Expr\Join::WITH, $qb->expr()->eq('bla.label', ':l'));
        $qb->setParameter('l', $label);
    }

    // Listes de réservations: partie Tri
    public function getListSort($qb)
    {
        $qb->orderBy('b.beginningDate', 'ASC');
    }

    // PLUS UTILISE: Affichage des réservations dans le calendrier
    public function getCalendarBookings(\App\Entity\File $file, \App\Entity\Planification $planification)
    {
        $qb = $this->createQueryBuilder('b');
        $qb->where('b.file = :file')->setParameter('file', $file);
        $qb->andWhere('b.planification = :planification')->setParameter('planification', $planification);
        $qb->orderBy('b.resource', 'ASC');
        $qb->addOrderBy('b.beginningDate', 'ASC');
        $query = $qb->getQuery();
        $results = $query->getResult();
        return $results;
    }
}
