<?php
namespace App\Entity;

use App\Entity\UserContext;
use App\Entity\PlanificationPeriod;
use App\Api\AdministrationApi;
use App\Api\PlanningApi;

class BookingPeriod
{
    private $planificationPeriod; // Période de planification
	private $before; // Appliquer une restriction avant la date du jour
	private $after; // Appliquer une restriction après la date du jour
	private $firstAllowedBookingDate; // Première date de réservation autorisée si indicateur before est vrai
	private $lastAllowedBookingDate; // Dernière date de réservation autorisée si indicateur after est vrai

	// newBookingBeginningDate et numberDays ne sont utilisés que pour le type Duplication
	function __construct($em, UserContext $userContext, PlanificationPeriod $planificationPeriod)
	{
	$this->setPlanificationPeriod($planificationPeriod);

	$file = $userContext->getCurrentFile();
	$fileAdministrator = $userContext->getCurrentUserFileAdministrator();

	if ($fileAdministrator) { // Les administrateurs du dossier ne sont pas limités en période de réservation
		$this->setBefore(false);
		$this->setAfter(false);
	} else {
		$this->setBefore(AdministrationApi::getFileBookingPeriodBefore($em, $file));
		$beforeType = AdministrationApi::getFileBookingPeriodBeforeType($em, $file);
		$beforeNumber = AdministrationApi::getFileBookingPeriodBeforeNumber($em, $file);
		$this->setAfter(AdministrationApi::getFileBookingPeriodAfter($em, $file));
		$afterType = AdministrationApi::getFileBookingPeriodAfterType($em, $file);
		$afterNumber = AdministrationApi::getFileBookingPeriodAfterNumber($em, $file);
	}

	if ($this->getBefore()) {
		$this->setFirstAllowedBookingDate(PlanningApi::getFirstDate($beforeType, $beforeNumber));
	} else {
		$this->setFirstAllowedBookingDate(new \DateTime());
	}
	if ($this->getAfter()) {
		$this->setLastAllowedBookingDate(PlanningApi::getLastDate($afterType, $afterNumber));
	} else {
		$this->setLastAllowedBookingDate(new \DateTime());
	}
	return $this;
	}

	public function getPlanificationPeriod(): ?PlanificationPeriod
	{
		return $this->planificationPeriod;
	}
	
	public function setPlanificationPeriod(?PlanificationPeriod $planificationPeriod): self
	{
		$this->planificationPeriod = $planificationPeriod;
		return $this;
	}

	public function setBefore(bool $before)
	{
	$this->before = $before;
	return $this;
	}

	public function getBefore()
	{
	return $this->before;
	}

	public function setAfter(bool $after)
	{
	$this->after = $after;
	return $this;
	}

	public function getAfter()
	{
	return $this->after;
	}

    public function setFirstAllowedBookingDate(\Datetime $firstAllowedBookingDate)
    {
	$this->firstAllowedBookingDate = $firstAllowedBookingDate;
	return $this;
    }

    public function getFirstAllowedBookingDate()
    {
    return $this->firstAllowedBookingDate;
    }

    public function setLastAllowedBookingDate(\Datetime $lastAllowedBookingDate)
    {
	$this->lastAllowedBookingDate = $lastAllowedBookingDate;
	return $this;
    }

	public function getLastAllowedBookingDate()
    {
    return $this->lastAllowedBookingDate;
    }
}
