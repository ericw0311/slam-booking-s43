<?php
namespace App\Entity;

use Psr\Log\LoggerInterface;

// Jour du planning: A signifie que les lignes de la journées sont gérées en tant que simple tableau
class PlanningDayA extends PlanningDay
{
	private $timetableLines;

	public function getTimetableLines()
	{
	return $this->timetableLines;
	}

	public function setTimetableLines($timetableLines): self
	{
	$this->timetableLines = $timetableLines;
	return $this;
	}

	public function getDisplayTimetableLines() // Les lignes de creneau horaire sont affichées si la journée est ni fermée ni au dela de la période de cloture
	{
	return ($this->getType() != 'C' and $this->getType() != 'X');
	}

	public function __construct(LoggerInterface $logger, $em, bool $ctrlBefore, bool $ctrlAfter, BookingPeriod $bookingPeriod, \Datetime $date)
	{
	parent::__construct($logger, $em, $ctrlBefore, $ctrlAfter, $bookingPeriod, $date);

	if ($this->getDisplayTimetableLines()) {
		$tlRepository = $em->getRepository(TimetableLine::Class);
		$timetableLines = $tlRepository->getTimetableLines($this->getPlanificationLine()->getTimetable());
		$this->setTimetableLines($timetableLines);
	}
	}
}
