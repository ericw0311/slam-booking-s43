<?php
namespace App\Entity;

use Psr\Log\LoggerInterface;

class PlanningLine
{
	private $type; // O, B, A, C, X: idem PlanningDay plus K: réservé (booked)
    private $timetableLine;
	private $timetableLinesList;

	public function getType(): ?string
	{
	return $this->type;
	}

	public function setType(string $type): self
	{
	$this->type = $type;
	return $this;
	}

    public function getTimetableLine(): ?TimetableLine
    {
	return $this->timetableLine;
    }

    public function setTimetableLine(?TimetableLine $timetableLine): self
    {
	$this->timetableLine = $timetableLine;
	return $this;
    }

	public function getTimetableLinesList(): ?string
	{
	return $this->timetableLinesList;
	}

	public function setTimetableLinesList(string $timetableLinesList): self
	{
	$this->timetableLinesList = $timetableLinesList;
	return $this;
	}

	public function __construct($em, string $dayType, Resource $resource, \DateTime $date, int $bookingID, TimetableLine $timetableLine, string $timetableLinesList)
	{
	$blRepository = $em->getRepository(BookingLine::Class);

	$this->setTimetableLine($timetableLine);
	$this->setTimetableLinesList($timetableLinesList);

	if ($dayType != 'O') {
		$this->setType($dayType); 
	} else {

		// Recherche d'une ligne de réservation existante.
		$bookingLineDB = $blRepository->findOneBy(array('resource' => $resource, 'ddate' => $date, 'timetable' => $timetableLine->getTimetable(), 'timetableLine' => $timetableLine));
		if ($bookingLineDB === null || $bookingLineDB->getBooking()->getID() == $bookingID) { // La ressource n'est pas réservée pour le créneau (ou bien on est en mise à jour de réservation et le créneau est réservé pour la réservation à mettre à jour).
			$this->setType('O');
		} else { // Une réservation existe sur ce créneau (ou une autre réservation que celle à mettre à jour)
			$this->setType('K'); 
		}
	}
	}
}
