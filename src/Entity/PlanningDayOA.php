<?php
namespace App\Entity;

use Psr\Log\LoggerInterface;

// Jour du planning: OA: objects array signifie que les lignes de la journées sont gérées en tant que tableau d'objets
// SERT A LA SELECTION DE LA PERIODE DE FIN DE RESERVATION
class PlanningDayOA extends PlanningDay
{
	private $timetableLinesList; // Liste des creneaux horaires correspondants à la sélection de la ligne comme période de fin de réservation.
	private $planningLines; // Liste des lignes
	private $lastDay; // Dernier jour affiché

	public function getTimetableLinesList(): ?string
	{
	return $this->timetableLinesList;
	}

	public function setTimetableLinesList(string $timetableLinesList): self
	{
	$this->timetableLinesList = $timetableLinesList;
	return $this;
	}

	public function addPlanningLine(PlanningLine $planningLine)
	{
	$this->planningLines[] = $planningLine;
	}
    
    public function getPlanningLines()
    {
	return $this->planningLines;
    }

	public function getNumberPlanningLines()
    {
	return count($this->planningLines);
    }

	public function isLastDay(): ?bool
	{
	return $this->lastDay;
	}

	public function setLastDay(bool $lastDay): self
	{
	$this->lastDay = $lastDay;
	return $this;
	}

	public function isClosed() // Tous les jours trouvés sont affichés sauf les jours pour lesquels la planification est fermée.
	{
	return ($this->getType() == 'C');
	}

	public function isOpened() // Seuls les creneaux horaires des jours ouverts sont pris en compte.
	{
	return ($this->getType() == 'O');
	}

	public function __construct(LoggerInterface $logger, $em, BookingPeriod $bookingPeriod,
		\Datetime $date, Resource $resource, int $bookingID, bool $firstDay, TimetableLine $beginningTimetableLine, 
		int $previousDaysNumberLines, string $previousDaysTimetableLinesList)
	{
	parent::__construct($logger, $em, $bookingPeriod->getBefore(), $bookingPeriod->getAfter(), $bookingPeriod, $date);
	
	$this->setLastDay($this->getType() == 'A' or $this->getType() == 'X'); // Si le jour est clôturé ou après la période de réservation, c'est le dernier affiché.

	$this->planningLines = array();
	$tlRepository = $em->getRepository(TimetableLine::Class);
	$continue = true;
	
	if ($this->isOpened()) {

		if ($firstDay) {
			$timetableLines = $tlRepository->getCurrentAndNextTimetableLines($this->getPlanificationLine()->getTimetable(), $beginningTimetableLine->getID());
		} else {
			$timetableLines = $tlRepository->getTimetableLines($this->getPlanificationLine()->getTimetable());
		}

		$dayTimetableLinesList = $date->format('Ymd').'+'.$this->getPlanificationLine()->getTimetable()->getID(); // Liste des créneaux horaires du jour
		$numberLines = 0;

		foreach ($timetableLines as $timetableLine) {
			$numberLines++;
			if ($continue) {

				$dayTimetableLinesList = ($numberLines <= 1) ? ($dayTimetableLinesList.'+'.$timetableLine->getID()) : ($dayTimetableLinesList.'*'.$timetableLine->getID());

				$lineTimetableLinesList = ($firstDay) ? $dayTimetableLinesList : ($previousDaysTimetableLinesList.'-'.$dayTimetableLinesList); // Liste des créneaux horaires de la ligne

				$planningLine = new PlanningLine($em, $this->getType(), $resource, $date, $bookingID, $timetableLine, $lineTimetableLinesList);
				$this->addPlanningLine($planningLine);
				
				if ($planningLine->getType() != 'O') { $this->setLastDay(true); } // Si une réservation est trouvée, c'est le dernier jour affiché.
				
				$continue = ($planningLine->getType() == 'O' and ($previousDaysNumberLines + $numberLines) < Constants::MAXIMUM_NUMBER_BOOKING_LINES); // On s'arrete des que le nombre maximum de lignes de réservation est atteint
			}
		}

		$this->setTimetableLinesList($dayTimetableLinesList);
	}
	}
}
