<?php
namespace App\Entity;

use App\Entity\User;
use App\Entity\File;
use App\Entity\PlanningLineA;
use App\Entity\BookingPeriod;

use App\Api\AdministrationApi;
use App\Api\PlanningApi;

use Psr\Log\LoggerInterface;

class PlanningContext
{
	private $planningType; // Type de planning: P = Planning, D = Dupplication de réservation
	private $numberLines; // Nombre de lignes pouvant etre affichees sur une page
	private $numberColumns; // Nombre de colonnes pouvant etre affichees sur une page
	private $days;

	// newBookingBeginningDate et numberDays ne sont utilisés que pour le type Duplication
	function __construct(LoggerInterface $logger, $em, User $user, File $file, BookingPeriod $bookingPeriod, 
		$planningType, \Datetime $beginningDate, \Datetime $newBookingBeginningDate, $numberDays)
	{
	$logger->info('PlanningContext DBG 1 _'.$beginningDate->format('Y-m-d H:i:s').'_');

	$this->planningType = $planningType;

	if ($this->getPlanningType() == 'D') { // En duplication, le nombre de colonnes est 1 et le nombre de lignes, le nombre de jours sur de la réservation à dupliquer
		$this->numberColumns =  1;
		$this->numberLines =  $numberDays+1;
	} else {
		$this->numberColumns = PlanningApi::getNumberColumns($em, $user);
		$this->numberLines = PlanningApi::getNumberLines($em, $user);
	}

	$this->days = array();
	$this->initDays($logger, $em, 1, $bookingPeriod, $beginningDate);

	if ($this->getPlanningType() == 'D') { // En duplication, on traite les jours de la réservation à créer.
		$this->initDays($logger, $em, 2, $bookingPeriod, $newBookingBeginningDate);
	}
	return $this;
	}

	// Planning: keyPrefix = 1
	// Duplication: keyPrefix = 1 pour la réservation origine et keyPrefix = 2 pour la réservation à créer
	public function initDays(LoggerInterface $logger, $em, $keyPrefix, BookingPeriod $bookingPeriod, \Datetime $beginningDate)
	{
	for($j = 1; $j <= $this->getNumberColumns(); $j++) {
		for($i = 1; $i <= $this->getNumberLines(); $i++) {
			$dayKey = $keyPrefix.'-'.$i.'-'.$j;
			$dayNum = ($j-1)*$this->getNumberLines() + ($i-1);
			$dayDate = clone $beginningDate;
			if ($dayNum > 0) {
				$dayDate->add(new \DateInterval('P'.$dayNum.'D'));
			}

			// Les administrateurs du dossier ne sont pas soumis aux restrictions de période
			// En duplication, on ne contrôle la date que pour le premier jour de la réservation à créer
			$ctrlBefore = ($bookingPeriod->getBefore() and ($this->getPlanningType() != 'D' or ($keyPrefix == 2 and $i == 1 and $j == 1)));
			$ctrlAfter = ($bookingPeriod->getAfter() and ($this->getPlanningType() != 'D' or ($keyPrefix == 2 and $i == 1 and $j == 1)));

			$this->days[$dayKey] = new PlanningDayA($logger, $em, $ctrlBefore, $ctrlAfter, $bookingPeriod, $dayDate);
		}
	}
	}

	public function getPlanningType()
	{
	return $this->planningType;
	}

	public function getNumberLines()
	{
	return $this->numberLines;
	}

	public function getNumberColumns()
	{
	return $this->numberColumns;
	}

	// Jour
	public function getDay($dayKey)
	{
	return $this->days[$dayKey];
	}

	// Nombre de jours affichés
	public function getNumberDays()
	{
	return $this->getNumberLines() * $this->getNumberColumns();
	}

	// Indique si on affiche la date pour chaque jour: systématiquement en duplication et si plusieurs jours affichés sur le planning
	public function displayDate()
	{
	return (($this->getPlanningType() == 'D') or ($this->getNumberDays() > 1));
	}

	// Première date de la période
	public function getFirstDate($keyPrefix)
	{
	return ($this->getDay($keyPrefix.'-1-1')->getDate());
	}

	// Première date de dupplication
	public function getFirstDuplicateDate()
	{
	return ($this->getFirstDate(2));
	}

	// Dernière date de la période
	public function getLastDate($keyPrefix)
	{
	return ($this->getDay($keyPrefix.'-'.$this->getNumberLines().'-'.$this->getNumberColumns())->getDate());
	}

	// Affichage des boutons dans le planning (pas pour la duplication)
	public function getDisplayButtons()
	{
	return ($this->getPlanningType() == 'P');
	}
}
