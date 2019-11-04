<?php
namespace App\Entity;

use Psr\Log\LoggerInterface;

class PlanningDay
{
	private $date;
	private $type; // O = ouvert, B = before (ouvert mais avant période de réservation), A = after (ouvert mais après période de réservation), C = fermé (closed), X = clôturé
	private $planificationLine;

	public function setDate(\Datetime $date)
	{
	$this->date = $date;
	return $this;
	}
    
    public function getDate()
	{
	return $this->date;
	}

	public function getType(): ?string
	{
	return $this->type;
	}

	public function setType(string $type): self
	{
	$this->type = $type;
	return $this;
	}

	public function getPlanificationLine(): ?PlanificationLine
	{
	return $this->planificationLine;
	}

	public function setPlanificationLine(?PlanificationLine $planificationLine): self
	{
	$this->planificationLine = $planificationLine;
	return $this;
	}

	// Les indicateurs ctrlBefore et ctrlAfter sont passés indépendamment de l'objet bookingPeriod car en dupplication de réservation on redéfinit ces indicateurs selon le jour du planning
	public function __construct(LoggerInterface $logger, $em, bool $ctrlBefore, bool $ctrlAfter, BookingPeriod $bookingPeriod, \Datetime $date)
	{
	$plRepository = $em->getRepository(PlanificationLine::Class);
	$this->setDate($date);

	$inPeriod = true;
	if (!$bookingPeriod->getPlanificationPeriod()->isEndDateNull()) { // La periode de planification est cloturée
		$interval = $this->getDate()->diff($bookingPeriod->getPlanificationPeriod()->getEndDate());
		$periodSign = $interval->format('%R');
		$logger->info('PlanningDay.construct DBG 1 _'.$bookingPeriod->getPlanificationPeriod()->getEndDate()->format('Y-m-d H:i:s').'_'.$periodSign.'_');

		if ($periodSign == '-') { $inPeriod = false; } // La date affichée est après la date de cloture de la période
	}

	if (!$inPeriod) {
		$this->setType('X'); // La journée est cloturée
		$this->setPlanificationLine(null);
	} else {
		$planificationLine = $plRepository->findOneBy(array('planificationPeriod' => $bookingPeriod->getPlanificationPeriod(), 'weekDay' => strtoupper($this->getDate()->format('D'))));

		if ($planificationLine === null || $planificationLine->getActive() < 1) {
			$this->setType('C'); // La journée est fermée
		} else {
			$this->setType('O'); // La journée est ouverte
		}
		$this->setPlanificationLine($planificationLine);
	}

	$beforeSign = '+';
	$afterSign = '+';

	if ($this->getType() == 'O') {
		if ($ctrlBefore) {
			$interval = $bookingPeriod->getFirstAllowedBookingDate()->diff($this->getDate());
			$beforeSign = $interval->format('%R');
		}
		if ($ctrlAfter) {
			$interval = $this->getDate()->diff($bookingPeriod->getLastAllowedBookingDate());
			$afterSign = $interval->format('%R');
		}
	}

	if ($beforeSign == '-') { $this->setType('B'); } // Avant période de réservation
	if ($afterSign == '-') { $this->setType('A'); } // Après période de réservation
	}
}
