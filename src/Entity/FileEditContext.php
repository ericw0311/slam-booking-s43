<?php
// Utilisé pour la page d'édition du dossier
namespace App\Entity;

use App\Api\AdministrationApi;
use App\Api\PlanningApi;

class FileEditContext
{
	protected $userFilesCount;
    protected $userTimetablesCount; // Nombre de grilles horaires saisies par l'utilisateur (type = T)
    protected $labelsCount;
    protected $resourcesCount;
    protected $bookingsCount;
	protected $fileAdministrator;
	protected $bookingUser;

	protected $before; // Appliquer une restriction avant la date du jour
	protected $beforeType; // Type de restriction avant la date du jour: JOUR, SEMAINE, MOIS, ANNEE
	protected $beforeNumber; // Nombre associé à la restriction avant la date du jour

	protected $after; // Appliquer une restriction après la date du jour
	protected $afterType; // Type de restriction après la date du jour: JOUR, SEMAINE, MOIS, ANNEE
	protected $afterNumber; // Nombre associé à la restriction après la date du jour

	private $firstBookingDate;
	private $lastBookingDate;

    public function setUserFilesCount($userFilesCount)
    {
    $this->userFilesCount = $userFilesCount;
    return $this;
    }

    public function getUserFilesCount()
    {
    return $this->userFilesCount;
    }

    public function setUserTimetablesCount($timetablesCount)
    {
    $this->userTimetablesCount = $timetablesCount;
    return $this;
    }

    public function getUserTimetablesCount()
    {
    return $this->userTimetablesCount;
    }

    public function setLabelsCount($labelsCount)
    {
    $this->labelsCount = $labelsCount;
    return $this;
    }

    public function getLabelsCount()
    {
    return $this->labelsCount;
    }

    public function setResourcesCount($resourcesCount)
    {
    $this->resourcesCount = $resourcesCount;
    return $this;
    }

    public function getResourcesCount()
    {
    return $this->resourcesCount;
    }

    public function setBookingsCount($bookingsCount)
    {
    $this->bookingsCount = $bookingsCount;
    return $this;
    }

    public function getBookingsCount()
    {
    return $this->bookingsCount;
    }

	public function getFileAdministrator()
	{
	return $this->fileAdministrator;
	}

	public function setFileAdministrator($fileAdministrator)
	{
	$this->fileAdministrator = $fileAdministrator;
	return $this;
	}

	public function getBookingUser()
	{
	return $this->bookingUser;
	}

	public function setBookingUser($bookingUser): self
	{
	$this->bookingUser = $bookingUser;
	return $this;
	}

	public function getBefore()
	{
	return $this->before;
	}

	public function setBefore($before): self
	{
	$this->before = $before;
	return $this;
	}

	public function getBeforeType()
	{
	return $this->beforeType;
	}

	public function setBeforeType($beforeType): self
	{
	$this->beforeType = $beforeType;
	return $this;
	}

	public function getBeforeNumber()
	{
	return $this->beforeNumber;
	}

	public function setBeforeNumber($beforeNumber): self
	{
	$this->beforeNumber = $beforeNumber;
	return $this;
	}

	public function getAfter()
	{
	return $this->after;
	}

	public function setAfter($after): self
	{
	$this->after = $after;
	return $this;
	}

	public function getAfterType()
	{
	return $this->afterType;
	}

	public function setAfterType($afterType): self
	{
	$this->afterType = $afterType;
	return $this;
	}

	public function getAfterNumber()
	{
	return $this->afterNumber;
	}

	public function setAfterNumber($afterNumber): self
	{
	$this->afterNumber = $afterNumber;
	return $this;
	}

    public function getFirstBookingDate()
    {
    return $this->firstBookingDate;
    }

    public function getLastBookingDate()
    {
    return $this->lastBookingDate;
    }

    function __construct($em, \App\Entity\File $file)
    {
    $ufRepository = $em->getRepository(UserFile::class);
    $this->setUserFilesCount($ufRepository->getUserFilesExceptFileCreatorCount($file));

    $lRepository = $em->getRepository(Label::class);
    $this->setLabelsCount($lRepository->getLabelsCount($file));

    $tRepository = $em->getRepository(Timetable::class);
    $this->setUserTimetablesCount($tRepository->getUserTimetablesCount($file));

    $rRepository = $em->getRepository(Resource::class);
    $this->setResourcesCount($rRepository->getResourcesCount($file));

    $bRepository = $em->getRepository(Booking::class);
    $this->setBookingsCount($bRepository->getAllBookingsCount($file));


    $this->setFileAdministrator(AdministrationApi::getFileBookingEmailAdministrator($em, $file));
	$this->setBookingUser(AdministrationApi::getFileBookingEmailUser($em, $file));

    $this->setBefore(AdministrationApi::getFileBookingPeriodBefore($em, $file));
    $this->setBeforeType(AdministrationApi::getFileBookingPeriodBeforeType($em, $file));
    $this->setBeforeNumber(AdministrationApi::getFileBookingPeriodBeforeNumber($em, $file));
    $this->setAfter(AdministrationApi::getFileBookingPeriodAfter($em, $file));
    $this->setAfterType(AdministrationApi::getFileBookingPeriodAfterType($em, $file));
    $this->setAfterNumber(AdministrationApi::getFileBookingPeriodAfterNumber($em, $file));

	if ($this->before) {
		$this->firstBookingDate = PlanningApi::getFirstDate($this->beforeType, $this->beforeNumber);
	} else {
		$this->firstBookingDate = new \DateTime();
	}
	if ($this->after) {
		$this->lastBookingDate = PlanningApi::getLastDate($this->afterType, $this->afterNumber);
	} else {
		$this->lastBookingDate = new \DateTime();
	}
    }
}
