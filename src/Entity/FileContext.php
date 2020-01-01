<?php
// Utilisé pour la page résumé du dossier: default/summary
namespace App\Entity;

class FileContext
{
	protected $userFileCount = 0;
	protected $userFileGroupCount = 0;
	protected $timetableCount = 0;
	protected $labelCount = 0;
	protected $activityCount = 0;
	protected $resourceCount = 0;
	protected $planificationCount = 0;
	protected $allBookingsCount = 0;
	protected $inProgressBookingsCount = 0;
	protected $currentUserBookingsCount = 0;
	protected $currentUserInProgressBookingsCount = 0;

	public function setUserFileCount($userFileCount)
	{
	$this->userFileCount = $userFileCount;
	return $this;
	}

	public function getUserFileCount()
	{
	return $this->userFileCount;
	}

	public function setUserFileGroupCount($userFileGroupCount)
	{
	$this->userFileGroupCount = $userFileGroupCount;
	return $this;
	}

	public function getUserFileGroupCount()
	{
	return $this->userFileGroupCount;
	}

	public function setTimetableCount($timetableCount)
	{
	$this->timetableCount = $timetableCount;
	return $this;
	}

	public function getTimetableCount()
	{
	return $this->timetableCount;
	}

	public function setLabelCount($labelCount)
	{
	$this->labelCount = $labelCount;
	return $this;
	}

	public function getLabelCount()
	{
	return $this->labelCount;
	}

	public function setActivityCount($activityCount)
	{
	$this->activityCount = $activityCount;
	return $this;
	}

	public function getActivityCount()
	{
	return $this->activityCount;
	}

	public function setResourceCount($resourceCount)
	{
	$this->resourceCount = $resourceCount;
	return $this;
	}

	public function getResourceCount()
	{
	return $this->resourceCount;
	}

	public function setPlanificationCount($planificationCount)
	{
	$this->planificationCount = $planificationCount;
	return $this;
	}

	public function getPlanificationCount()
	{
	return $this->planificationCount;
	}

	public function setAllBookingsCount($bookingsCount)
	{
	$this->allBookingsCount = $bookingsCount;
	return $this;
	}

	public function getAllBookingsCount()
	{
	return $this->allBookingsCount;
	}

	public function setInProgressBookingsCount($bookingsCount)
	{
	$this->inProgressBookingsCount = $bookingsCount;
	return $this;
	}

	public function getInProgressBookingsCount()
	{
	return $this->inProgressBookingsCount;
	}

	public function setCurrentUserBookingsCount($bookingsCount)
	{
	$this->currentUserBookingsCount = $bookingsCount;
	return $this;
	}

	public function getCurrentUserBookingsCount()
	{
	return $this->currentUserBookingsCount;
	}

	public function setCurrentUserInProgressBookingsCount($bookingsCount)
	{
	$this->currentUserInProgressBookingsCount = $bookingsCount;
	return $this;
	}

	public function getCurrentUserInProgressBookingsCount()
	{
	return $this->currentUserInProgressBookingsCount;
	}

    function __construct($em, \App\Entity\UserContext $userContext)
    {
	$ufRepository = $em->getRepository(UserFile::Class);
	$lFile = $userContext->getCurrentFile();
	$lFileAdministrator = $userContext->getCurrentUserFileAdministrator();
	$lUserFile = $userContext->getCurrentUserFile();

	$this->setUserFileCount($ufRepository->getUserFilesCount($lFile));

	$ufgRepository = $em->getRepository(UserFileGroup::Class);
	$this->setUserFileGroupCount($ufgRepository->getUserFileGroupsCount($lFile));

	$lRepository = $em->getRepository(Label::Class);
	$this->setLabelCount($lRepository->getLabelsCount($lFile));

	$tRepository = $em->getRepository(Timetable::Class);
	$this->setTimetableCount($tRepository->getTimetablesCount($lFile));

	$rRepository = $em->getRepository(Resource::Class);
	$this->setResourceCount($rRepository->getResourcesCount($lFile));

	$pRepository = $em->getRepository(Planification::Class);
	$this->setPlanificationCount($pRepository->getPlanificationsCount($lFile));

	$bRepository = $em->getRepository(Booking::Class);
	$blRepository = $em->getRepository(BookingLine::class);

	if ($lFileAdministrator) { // L'utilisateur est adminsitrateur du dossier
		$this->setAllBookingsCount($bRepository->getAllBookingsCount($lFile));
	} else { // L'utilisateur n'est pas adminsitrateur du dossier: accès aux ressources d'après les vues utilisateur
		$this->setAllBookingsCount($bRepository->getUserFileResourcesAllBookingsCount($lFile, $blRepository->getResourceUserFileQB($lUserFile)));
	}

	if ($this->getAllBookingsCount() <= 0) {
		$this->setInProgressBookingsCount(0);
		$this->setCurrentUserBookingsCount(0);
		$this->setCurrentUserInProgressBookingsCount(0);
	} else {

		if ($lFileAdministrator) { // L'utilisateur est adminsitrateur du dossier
			$this->setInProgressBookingsCount($bRepository->getFromDatetimeBookingsCount($lFile, new \DateTime()));
		} else { // L'utilisateur n'est pas adminsitrateur du dossier: accès aux ressources d'après les vues utilisateur
			$this->setInProgressBookingsCount($bRepository->getUserFileResourcesFromDatetimeBookingsCount($lFile, new \DateTime(), $blRepository->getResourceUserFileQB($lUserFile)));
		}
		$this->setCurrentUserBookingsCount($bRepository->getUserFileBookingsCount($lFile, $lUserFile));
		if ($this->getInProgressBookingsCount() <= 0 || $this->getCurrentUserBookingsCount() <= 0) {
			$this->setCurrentUserInProgressBookingsCount(0);
		} else {
			$this->setCurrentUserInProgressBookingsCount($bRepository->getUserFileFromDatetimeBookingsCount($lFile, $lUserFile, new \DateTime()));
		}
	}
    return $this;
    }
}
