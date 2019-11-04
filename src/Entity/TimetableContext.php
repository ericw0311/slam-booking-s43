<?php
namespace App\Entity;

class TimetableContext
{
    protected $planificationsCount;
    protected $bookingsCount;

    public function setPlanificationsCount($planificationsCount)
    {
        $this->planificationsCount = $planificationsCount;
        return $this;
    }

    public function setBookingsCount($bookingsCount)
    {
        $this->bookingsCount = $bookingsCount;
        return $this;
    }

    public function getPlanificationsCount()
    {
        return $this->planificationsCount;
    }

    public function getBookingsCount()
    {
        return $this->bookingsCount;
    }

    public function __construct($em, \App\Entity\File $file, \App\Entity\Timetable $timetable)
    {
        $pRepository = $em->getRepository(Planification::class);
        $this->setPlanificationsCount($pRepository->getTimetablePlanificationsCount($file, $timetable));

        $bRepository = $em->getRepository(Booking::class);
        $blRepository = $em->getRepository(BookingLine::class);
        $numberBookings = $bRepository->getTimetableBookingsCount($file, $timetable, $blRepository->getTimetableBookingLineQB());
        $this->setBookingsCount($numberBookings);

        return $this;
    }
}
