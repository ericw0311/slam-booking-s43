<?php
namespace App\Api;

use Psr\Log\LoggerInterface;

use App\Entity\File;
use App\Entity\UserFile;
use App\Entity\UserContext;
use App\Entity\BookingPeriod;
use App\Entity\Resource;
use App\Entity\Label;
use App\Entity\TimetableLine;
use App\Entity\PlanificationPeriod;
use App\Entity\PlanificationLine;
use App\Entity\PlanningDayOA;
use App\Entity\Booking;
use App\Entity\BookingLine;
use App\Entity\BookingUser;
use App\Entity\BookingLabel;
use App\Entity\BookingDateNDB;
use App\Entity\BookingPeriodNDB;
use App\Entity\BookingNDB;
use App\Entity\BookingDuplication;
use App\Entity\SelectedEntity;
use App\Entity\AddEntity;
use App\Entity\Constants;

use App\Api\ResourceApi;

class BookingApi
{
	// firstDayNumber: Premier jour affiché
	// bookingID: Identifient de la réservation mise à jour (0 si création de réservation)
	static function getEndPeriods(LoggerInterface $logger, $em, File $file, BookingPeriod $bookingPeriod, Resource $resource,
		\Datetime $beginningDate, TimetableLine $beginningTimetableLine, int $bookingID, int $firstDayNumber, int &$nextFirstDayNumber)
	{
	$endPeriodDays = array();
	$dayIndex = 0;
	$numberDays = 0;
	$numberPlanningLines = 0;
	$continue = true;
	$reachFollowingDays = true; // Atteindre les jours suivants. Sert au calcul du premier jour affiché suivant
	$firstDay = true;
	$previousDaysTimetableLinesList = '';

	while ($continue) {
		// $date = clone $beginningDate;
		$date = new \DateTime($beginningDate->format('Y-m-d')); // On ignor la partie heures-minutes-secondes
		if ($dayIndex > 0) { $date->add(new \DateInterval('P'.$dayIndex.'D')); }

		$logger->info('BookingApi.getEndPeriods DBG 1 _'.$date->format('Y-m-d H:i:s').'_');

		$planningDay = new PlanningDayOA($logger, $em, $bookingPeriod, $date, $resource, $bookingID,
			$firstDay, $beginningTimetableLine, $numberPlanningLines, $previousDaysTimetableLinesList);

		if (!$planningDay->isClosed()) { // La journée n'est pas fermée.

			$numberDays++;
			if ($planningDay->isOpened()) {

				$numberPlanningLines += $planningDay->getNumberPlanningLines();

				$previousDaysTimetableLinesList = $firstDay ?
					$planningDay->getTimetableLinesList() :
					($previousDaysTimetableLinesList.'-'.$planningDay->getTimetableLinesList());

				$firstDay = false;
			}

			if ($numberDays >= $firstDayNumber) { $endPeriodDays[] = $planningDay; }
		}

		if ($planningDay->isLastDay() or // Dernier jour affiché
			$numberDays >= ($firstDayNumber - 1 + Constants::MAXIMUM_NUMBER_BOOKING_DATES_DISPLAYED) or // Nombre maximum de jours affichés atteint
			($numberPlanningLines >= Constants::MAXIMUM_NUMBER_BOOKING_LINES)) // Nombre maximum de lignes pour une reservation atteint
			{ $continue = false; }

		if ($planningDay->isLastDay() or // Dernier jour affiché
			($numberPlanningLines >= Constants::MAXIMUM_NUMBER_BOOKING_LINES)) // Nombre maximum de lignes pour une reservation atteint
			{ $reachFollowingDays = false; }

		$dayIndex++;
	}

	// Premiere date affichee suivante
	$nextFirstDayNumber = $reachFollowingDays ? ($firstDayNumber + Constants::MAXIMUM_NUMBER_BOOKING_DATES_DISPLAYED) : 0;
	return $endPeriodDays;
	}

	// Retourne une chaine correspondant à la liste des utilisateurs d'une réservation
	static function getBookingUsersUrl($em, \App\Entity\Booking $booking)
	{
	$buRepository = $em->getRepository(BookingUser::Class);
	$bookingUsersDB = $buRepository->getBookingUsers($booking);
	if (count($bookingUsersDB) <= 0) {
		return '';
	}
	$premier = true;
	foreach ($bookingUsersDB as $bookingUser) {
		if ($premier) {
			$url = $bookingUser['userFileID'];
		} else {
			$url .= '-'.$bookingUser['userFileID'];
		}
		$premier = false;
	}
	return $url;
	}

	// Retourne la liste des noms des utilisateurs d'une réservation
	static function getBookingUserPlanningInfo($em, \App\Entity\Booking $booking, \App\Entity\UserFile $currentUserFile, &$numberUsers)
	{
	$buRepository = $em->getRepository(BookingUser::Class);
	$bookingUsers = $buRepository->findBy(array('booking' => $booking), array('oorder' => 'asc'));
	if (count($bookingUsers) <= 0) { // Ce cas ne doit pas arriver. Toute réservation a au moins un utilisateur. Mais si cela arrive, on initialise la liste des utilisateurs avec l'utilisateur courant
		$numberUsers = 1;
		return $currentUserFile->getFirstAndLastName();
	}

	$numberUsers = count($bookingUsers);
	return $bookingUsers[0]->getUserFiles()->getFirstAndLastName();
	}

	// Gestion des étiquettes des réservations
	// Retourne un tableau des étiquettes sélectionnées
	// labelIDList: Liste des ID des étiquettes sélectionnées
	static function getSelectedLabels($em, $labelIDList)
	{
	$labelIDArray = array();
	if (strcmp($labelIDList, "0") != 0) { // La chaine '0' équivaut à une chaine vide
		$labelIDArray = explode("-", $labelIDList);
	}
    $lRepository = $em->getRepository(Label::Class);
	$selectedLabels = array();
	$i = 0;
    foreach ($labelIDArray as $labelID) {
		$labelDB = $lRepository->find($labelID);
		if ($labelDB !== null) {
			$label = new SelectedEntity(); // classe générique des entités sélectionnées
			$label->setId($labelDB->getId());
			$label->setName($labelDB->getName());
			$label->setImageName("label-32.png");
			$labelIDArray_tprr = $labelIDArray;
			unset($labelIDArray_tprr[$i]);
			$label->setEntityIDList_unselect((count($labelIDArray_tprr) > 0) ? implode('-', $labelIDArray_tprr) : '0'); // Liste des étiquettes sélectionnées si l'utilisateur désélectionne l'étiquette
			if (count($labelIDArray) > 1) {
				if ($i > 0) {
					$labelIDArray_tprr = $labelIDArray;
					$labelIDArray_tprr[$i] = $labelIDArray_tprr[$i-1];
					$labelIDArray_tprr[$i-1] = $labelID;
					$label->setEntityIDList_sortBefore(implode('-', $labelIDArray_tprr)); // Liste des étiquettes sélectionnées si l'utilisateur remonte l'étiquette dans l'ordre de tri
				}
				if ($i < count($labelIDArray)-1) {
					$labelIDArray_tprr = $labelIDArray;
					$labelIDArray_tprr[$i] = $labelIDArray_tprr[$i+1];
					$labelIDArray_tprr[$i+1] = $labelID;
					$label->setEntityIDList_sortAfter(implode('-', $labelIDArray_tprr)); // Liste des étiquettes sélectionnées si l'utilisateur redescend l'étiquette dans l'ordre de tri
				}
			}
			$i++;
			array_push($selectedLabels, $label);
		}
	}
	return $selectedLabels;
    }

	// Retourne un tableau des étiquettes pouvant être ajoutées à une réservation
	static function getAvailableLabels($labelsDB, $selectedLabelIDList)
    {
	$selectedLabelIDArray = array();
	if (strcmp($selectedLabelIDList, "0") != 0) { // La chaine '0' équivaut à une chaine vide
		$selectedLabelIDArray = explode("-", $selectedLabelIDList);
	}
	$availableLabels = array();
    foreach ($labelsDB as $labelDB) {
		if (array_search($labelDB->getId(), $selectedLabelIDArray) === false) {
			$label = new AddEntity(); // classe générique des entités pouvant être ajoutées à la sélection
			$label->setId($labelDB->getId());
			$label->setName($labelDB->getName());
			$label->setImageName("label-32.png");
			$label->setEntityIDList_select((count($selectedLabelIDArray) < 1) ? $labelDB->getId() : ($selectedLabelIDList.'-'.$labelDB->getId())); // Liste des étiquettes sélectionnées si l'utilisateur sélectionne l'étiquette
			array_push($availableLabels, $label);
		}
	}
	return $availableLabels;
    }

	// Retourne un tableau des étiquettes pouvant être ajoutées à une réservation
	static function initAvailableLabels($em, \App\Entity\File $file, $selectedLabelIDList)
	{
	$lRepository = $em->getRepository(Label::Class);
	$labelsDB = $lRepository->getLabels	($file);
	return BookingApi::getAvailableLabels($labelsDB, $selectedLabelIDList);
	}

	// Retourne une chaine correspondant à la liste des étiquettes d'une réservation
	static function getBookingLabelsUrl($em, \App\Entity\Booking $booking)
	{
	$blRepository = $em->getRepository(BookingLabel::Class);
	$bookingLabelsDB = $blRepository->getBookingLabels($booking);
	if (count($bookingLabelsDB) <= 0) {
		return '0';
	}
	$premier = true;
	foreach ($bookingLabelsDB as $bookingLabel) {
		if ($premier) {
			$url = $bookingLabel['labelID'];
		} else {
			$url .= '-'.$bookingLabel['labelID'];
		}
		$premier = false;
	}
	return $url;
	}

	// Retourne un tableau d'étiquettes à partir d'une liste d'ID
	static function getLabels($em, $labelIDList)
	{
	$labelIDArray = array();
	if (strcmp($labelIDList, "0") != 0) { // La chaine '0' équivaut à une chaine vide
		$labelIDArray = explode("-", $labelIDList);
	}
	$labels = array();
	$lRepository = $em->getRepository(Label::Class);
	foreach ($labelIDArray as $labelID) {
		$label = $lRepository->find($labelID);
		if ($label !== null) {
			$labels[] = $label;
		}
	}
	return $labels;
	}

	// Retourne un tableau d'identifiants d'étiquettes à partir d'une liste d'ID
	static function getLabelsID($labelIDList)
	{
	$labelsID = array();
	if (strcmp($labelIDList, "0") != 0) {
		$labelsID = explode("-", $labelIDList);
	}
	return $labelsID;
	}

	// Retourne un tableau des étiquettes d'une réservation
	static function getBookingLabelPlanningInfo($em, \App\Entity\Booking $booking, &$numberLabels)
	{
	$blRepository = $em->getRepository(BookingLabel::Class);
	$bookingLabels = $blRepository->findBy(array('booking' => $booking), array('oorder' => 'asc'));

	if (count($bookingLabels) <= 0) {
		$numberLabels = 0;
		return '';
	}

	$numberLabels = count($bookingLabels);
	return $bookingLabels[0]->getLabel()->getName();
	}

	// Retourne les informations de début et de fin de réservation à partir d'une liste de périodes contenue dans une Url
    static function getBookingLinesUrlBeginningAndEndPeriod($em, $timetableLinesList, &$beginningDate, &$beginningTimetableLine, &$endDate, &$endTimetableLine)
	{
	$cellArray  = explode("-", $timetableLinesList);
    $ttlRepository = $em->getRepository(TimetableLine::Class);
	list($beginningDateString, $beginningTimetableID, $beginningTimetableLinesList) = explode("+", $cellArray[0]);
	$beginningDate = date_create_from_format("Ymd", $beginningDateString);
	$beginningTimetableLines = explode("*", $beginningTimetableLinesList);
	$beginningTimetableLineID = $beginningTimetableLines[0];
	$beginningTimetableLine = $ttlRepository->find($beginningTimetableLineID);
	list($endDateString, $endTimetableID, $endTimetableLinesList) = explode("+", $cellArray[count($cellArray)-1]);
	$endDate = date_create_from_format("Ymd", $endDateString);
	$endTimetableLines = explode("*", $endTimetableLinesList);
	$endTimetableLineID = $endTimetableLines[count($endTimetableLines)-1];
	$endTimetableLine = $ttlRepository->find($endTimetableLineID);
	}

	// Convertit une URL comprenant une liste de grilles horaires (pour réservation) en un tableau de grilles horaires
	static function getTimetableLines($timetableLinesUrl)
	{
	$timetableLineArray = array();
	$urlArray  = explode("-", $timetableLinesUrl);
	foreach ($urlArray as $urlDate) {
		list($dateString, $timetableID, $timetableLinesList) = explode("+", $urlDate);
		$timetableLineIDArray = explode("*", $timetableLinesList);
		foreach ($timetableLineIDArray as $timetableLineID) {
			$timetableLineArray[] = ($dateString.'-'.$timetableID.'-'.$timetableLineID);
		}
	}
	return $timetableLineArray;
	}


	static function getPlanningBookings($em, \App\Entity\File $file, \Datetime $beginningDate, \Datetime $endDate, \App\Entity\Planification $planification, \App\Entity\PlanificationPeriod $planificationPeriod, \App\Entity\UserFile $currentUserFile)
	{
	$bRepository = $em->getRepository(Booking::Class);

	$evenResourcesID = ResourceApi::getEvenPlanifiedResourcesID($em, $planificationPeriod);
	$bookingsDB = $bRepository->getPlanningBookings($file, $beginningDate, $endDate, $planification, $planificationPeriod);

	return BookingApi::getPlanningBookingArray($em, $currentUserFile, $bookingsDB, 'P', $evenResourcesID, 0, 0);
	}


	static function getDuplicateBookings($em, \App\Entity\File $file, \Datetime $beginningDate, \Datetime $endDate, \Datetime $newBookingBeginningDate, \Datetime $newBookingEndDate,
		\App\Entity\Planification $planification, \App\Entity\PlanificationPeriod $planificationPeriod, \App\Entity\Booking $originBooking, $newBookingID, \App\Entity\UserFile $currentUserFile)
	{
	$bRepository = $em->getRepository(Booking::Class);

	$evenResourcesID = array();
	$bookingsDB = $bRepository->getDuplicateBookings($file, $beginningDate, $endDate, $newBookingBeginningDate, $newBookingEndDate,
		$planification, $planificationPeriod, $originBooking->getResource());

	return BookingApi::getPlanningBookingArray($em, $currentUserFile, $bookingsDB, 'D', $evenResourcesID, $originBooking->getID(), $newBookingID);
	}

	// Retourne le tableau des réservations pour affichage dans un planning
	// bookingsDB: Ressources interrogées en base de données
	// planningType: P = Planning, C = réservations Cycliques
	// evenResourcesID: Tableau des ressources ayant un numéro d'ordre pair: Pour planningType P = Planning
	// bookingID: Réservation: Pour planningType D = Duplication de réservation
	static function getPlanningBookingArray($em, \App\Entity\UserFile $currentUserFile, $bookingsDB, $planningType, $evenResourcesID, $originBookingID, $newBookingID)
	{
	$bRepository = $em->getRepository(Booking::Class);

	$bookings = array();
	if (count($bookingsDB) <= 0) {
		return $bookings;
	}

	$memo_date = "00000000";
	$memo_bookingID = 0;
	$memo_resourceID = 0;
	$currentBookingHeaderKey = "";
	$bookingTimetableLinesCount = 0; // Compteur des lignes de la reservation courante.
	$resourceBookingCount = 0; // Compteur des reservations de la ressource courante.
	$numberUsers = 1;
	$numberLabels = 0;

	foreach ($bookingsDB as $booking) {
		$key = $booking['date']->format('Ymd').'-'.$booking['planificationID'].'-'.$booking['planificationPeriodID'].'-'.$booking['planificationLineID'].'-'.$booking['resourceID'].'-'.$booking['timetableID'].'-'.$booking['timetableLineID'];

		if ($memo_bookingID > 0 && ($booking['bookingID'] <> $memo_bookingID || $booking['date']->format('Ymd') <> $memo_date)) { // On a parcouru une reservation ou rupture de date.
			$bookings[$currentBookingHeaderKey]->setNumberTimetableLines($bookingTimetableLinesCount);

			$userString = BookingApi::getBookingUserPlanningInfo($em, $bRepository->find($memo_bookingID), $currentUserFile, $numberUsers);
			$bookings[$currentBookingHeaderKey]->setFirstUserName($userString);
			$bookings[$currentBookingHeaderKey]->setNumberUsers($numberUsers);

			$labelString = BookingApi::getBookingLabelPlanningInfo($em, $bRepository->find($memo_bookingID), $numberLabels);
			$bookings[$currentBookingHeaderKey]->setFirstLabelName($labelString);
			$bookings[$currentBookingHeaderKey]->setNumberLabels($numberLabels);

			$bookings[$currentBookingHeaderKey]->setNote($bRepository->find($memo_bookingID)->getNote());
			$bookings[$currentBookingHeaderKey]->setUserId($bRepository->find($memo_bookingID)->getUser()->getID());
			$bookingTimetableLinesCount = 0;
			$resourceBookingCount++;
		}

		if ($booking['date']->format('Ymd') <> $memo_date || $booking['resourceID'] <> $memo_resourceID) { // On change de date ou de ressource.
			$resourceBookingCount = 0;
		}

		$bookingTimetableLinesCount++;

		if ($booking['bookingID'] <> $memo_bookingID || $booking['date']->format('Ymd') <> $memo_date) {
			$cellType = 'H';
			$currentBookingHeaderKey = $key;
		} else {
			$cellType = 'L';
		}

		if ($planningType == 'D') {
			// Duplication de réservation: La réservation traitée a une couleur bleue, les autres ont une couleur rouge pale
			$cellClass = ((($booking['bookingID'] == $originBookingID) or ($booking['bookingID'] == $newBookingID)) ? 'blue' : 'pale-red');
		} else {
			// Planning: La couleur des réservations est alternée à la fois entre ressources (utilisation du tableau des ressources d'ordre pair) et entre réservations d'une même journée (Compteur resourceBookingCount)
			$cellClass = (in_array($booking['resourceID'], $evenResourcesID) ? ((($resourceBookingCount % 2) < 1) ? 'pale-green' : 'pale-yellow') : ((($resourceBookingCount % 2) < 1) ? 'pale-blue' : 'pale-red'));
		}

		$bookingNDB = new BookingNDB($booking['bookingID'], $cellType, $cellClass);
		$bookings[$key] = $bookingNDB;

		$memo_bookingID = $booking['bookingID'];
		$memo_resourceID = $booking['resourceID'];
		$memo_date = $booking['date']->format('Ymd');
	}

	$bookings[$currentBookingHeaderKey]->setNumberTimetableLines($bookingTimetableLinesCount); // Derniere reservation

	$userString = BookingApi::getBookingUserPlanningInfo($em, $bRepository->find($memo_bookingID), $currentUserFile, $numberUsers);
	$bookings[$currentBookingHeaderKey]->setFirstUserName($userString);
	$bookings[$currentBookingHeaderKey]->setNumberUsers($numberUsers);

	$labelString = BookingApi::getBookingLabelPlanningInfo($em, $bRepository->find($memo_bookingID), $numberLabels);
	$bookings[$currentBookingHeaderKey]->setFirstLabelName($labelString);
	$bookings[$currentBookingHeaderKey]->setNumberLabels($numberLabels);

	$bookings[$currentBookingHeaderKey]->setNote($bRepository->find($memo_bookingID)->getNote());
	$bookings[$currentBookingHeaderKey]->setUserId($bRepository->find($memo_bookingID)->getUser()->getID());
	return $bookings;
	}

	// Retourne une chaine correspondant à la liste des creneaux horaires d'une réservation
	static function getBookingLinesUrl($em, \App\Entity\Booking $booking)
	{
	$blRepository = $em->getRepository(BookingLine::Class);
	$bookingLinesDB = $blRepository->getBookingLines($booking);
	if (count($bookingLinesDB) <= 0) {
		return '';
	}

	// On construit une chaine comprenant toutes périodes de la réservation.
	// Les périodes sont regroupées par date séparées par un -
	// Pour chaque date, on a le codage date + timetableID + timetableLineIDList
	// timetableLineIDList est la liste des timetableLineID séparés par un *
	$premier = true;
	foreach ($bookingLinesDB as $bookingLine) {
		if ($premier) {
			$url = $bookingLine['date']->format('Ymd').'+'.$bookingLine['timetableID'].'+'.$bookingLine['timetableLineID'];

		} else if ($bookingLine['date']->format('Ymd') != $memo_date) {
			$url .= '-'.$bookingLine['date']->format('Ymd').'+'.$bookingLine['timetableID'].'+'.$bookingLine['timetableLineID'];
		} else {
			$url .= '*'.$bookingLine['timetableLineID'];
		}
		$premier = false;
		$memo_date = $bookingLine['date']->format('Ymd');
	}
	return $url;
	}

	// Retourne la liste d'emails des destinataires du mail informant de la création/mise à jour/suppression des réservations
	static function getBookingUserEmailArray($em, \App\Entity\Booking $booking, $fileAdministrator, $bookingUser)
	{
	$ufRepository = $em->getRepository(UserFile::Class);
	$buRepository = $em->getRepository(BookingUser::Class);

	$emailArray = array();

	if ($fileAdministrator) { // Administrateurs du dossier
		$fileAdministrators = $ufRepository->getUserFileAdministrators($booking->getFile());

		foreach ($fileAdministrators as $userFile) {
			array_push($emailArray, $userFile->getEmail());
		}
	}

	if ($bookingUser) { // Utilisateurs de la réservation
		$bookingUsers = $buRepository->findBy(array('booking' => $booking), array('oorder' => 'asc'));

		foreach ($bookingUsers as $bookingUser) {
			// Je ne parviens pas à mettre le nom des utilisateurs en clair... array_push($emailArray, [$bookingUser->getUserFiles()->getEmail() => $bookingUser->getUserFiles()->getFirstAndLastName()]);
			// alors que ça, ça marche bien... ->setTo(['eric.pierre.willard@gmail.com', 'maxence.willard@gmail.com' => 'Maxence Willard'])

			if (!$fileAdministrator or !$bookingUser->getUserFiles()->getAdministrator()) { // On traite le cas ou l'utilisateur est déjà dans la liste en tant que administrateur du dossier.
				array_push($emailArray, $bookingUser->getUserFiles()->getEmail());
			}
		}
	}
	return $emailArray;
	}


	// Contrôle si une réservation peut être duppliquée. On parcourt les lignes de réservation et on recherche sur les conditions de la clé unique
	// Retourne 0 si aucune ligne de réservation trouvée, et l'ID de la première ligne sinon
	static function ctrlDuplicateBooking($em, \App\Entity\Booking $booking, $gap)
	{
	$blRepository = $em->getRepository(BookingLine::Class);
	$bookingLinesDB = $blRepository->getBookingLines($booking);

	$bookingLines = $blRepository->findBy(array('booking' => $booking), array('id' => 'asc'));
	foreach ($bookingLines as $bookingLine) {

		$newBookingLineDate = clone $bookingLine->getDate();
		$newBookingLineDate->add(new \DateInterval('P'.$gap.'D'));

		$newBookingLineDate = $blRepository->findOneBy(array('resource' => $bookingLine->getResource(), 'ddate' => $newBookingLineDate, 'timetable' => $bookingLine->getTimetable(), 'timetableLine' => $bookingLine->getTimetableLine()));
		if ($newBookingLineDate !== null) {
			return $newBookingLineDate->getId();
		}
	}
	return 0;
	}

	// Dupplication d'une réservation.
	static function duplicateBooking($em, \App\Entity\Booking $booking, $gap, $connectedUser, $currentFile)
	{
	$bliRepository = $em->getRepository(BookingLine::Class);
	$buRepository = $em->getRepository(BookingUser::Class);
	$blaRepository = $em->getRepository(BookingLabel::Class);
	$plRepository = $em->getRepository(PlanificationLine::Class);

	$firstBookingLine = $bliRepository->getFirstBookingLine($booking);
	$lastBookingLine = $bliRepository->getLastBookingLine($booking);

	$newBookingBeginningDate = clone $firstBookingLine->getDate();
	$newBookingBeginningDate->add(new \DateInterval('P'.$gap.'D'));

	$newBookingEndDate = clone $lastBookingLine->getDate();
	$newBookingEndDate->add(new \DateInterval('P'.$gap.'D'));

	$newBooking = new Booking($connectedUser, $currentFile, $booking->getPlanification(), $booking->getResource());

	$newBooking->setBeginningDate(date_create_from_format('YmdHi', $newBookingBeginningDate->format('Ymd').$firstBookingLine->getTimetableLine()->getBeginningTime()->format('Hi')));
	$newBooking->setEndDate(date_create_from_format('YmdHi', $newBookingEndDate->format('Ymd').$lastBookingLine->getTimetableLine()->getEndTime()->format('Hi')));

	$newBooking->setNote($booking->getNote());
	// $newBooking->setFormNote($booking->getFormNote());
	$em->persist($newBooking);

	$bookingLines = $bliRepository->findBy(array('booking' => $booking), array('id' => 'asc'));
	foreach ($bookingLines as $bookingLine) {

		$newBookingLineDate = clone $bookingLine->getDate();
		$newBookingLineDate->add(new \DateInterval('P'.$gap.'D'));

		$newBookingLine = new BookingLine($connectedUser, $newBooking, $bookingLine->getResource());
		$newBookingLine->setDate($newBookingLineDate);
		$newBookingLine->setPlanification($bookingLine->getPlanification());
		$newBookingLine->setPlanificationPeriod($bookingLine->getPlanificationPeriod());
		// La référence à la ligne de planification est recherchée car si la nouvelle réservation et l'ancienne ne sont pas sur un même jour de la semaine (ce qui est théoriquement possible) cette référence n'est pas la même entre les deux réservations
		$newBookingLine->setPlanificationLine($plRepository->findOneBy(array('planificationPeriod' => $bookingLine->getPlanificationPeriod(), 'weekDay' => strtoupper($newBookingLineDate->format('D')))));
		$newBookingLine->setTimetable($bookingLine->getTimetable());
		$newBookingLine->setTimetableLine($bookingLine->getTimetableLine());
		$em->persist($newBookingLine);
	}

	$bookingUsers = $buRepository->findBy(array('booking' => $booking), array('id' => 'asc'));
	foreach ($bookingUsers as $bookingUser) {

		$newBookingUser = new BookingUser($connectedUser, $newBooking, $bookingUser->getUserFiles());
		$newBookingUser->setOrder($bookingUser->getOrder());
		$em->persist($newBookingUser);
	}

	$bookingLabels = $blaRepository->findBy(array('booking' => $booking), array('id' => 'asc'));
	foreach ($bookingLabels as $bookingLabel) {

		$newBookingLabel = new BookingLabel($connectedUser, $newBooking, $bookingLabel->getLabel());
		$newBookingLabel->setOrder($bookingLabel->getOrder());
		$em->persist($newBookingLabel);
	}

	$bookingDuplication = new BookingDuplication($connectedUser, $booking, $newBookingBeginningDate, $gap, $newBooking);
	$em->persist($bookingDuplication);

	$em->flush();
	return $newBooking->getID();
	}

	// Détermine un type d'autorisation de mise à jour et suppression d'une réservation: O = autorisé. U = non autorisé car saisie par un autre utilisateur. P = non autorisé car en dehors de la période de réservation
	static function getBookingAuthorisationType(UserContext $userContext, BookingPeriod $bookingPeriod, Booking $booking, \Datetime $beginningDate, \Datetime $endDate)
	{
	$authorisationType = 'O';

	if ($userContext->getCurrentUserFileAdministrator() or $booking->getUser()->getID() == $userContext->getUser()->getID()) {
		$authorisationType = 'O';
	} else {
		$authorisationType = 'U'; // L'utilisateur n'est pas adminsitrateur du dossier et n'est pas à l'origine de la réservation.
	}

	if ($authorisationType != 'O') { return ($authorisationType); }

	$beforeSign = '+';

	if ($bookingPeriod->getBefore()) {
		$interval = $bookingPeriod->getFirstAllowedBookingDate()->diff($endDate);
		$beforeSign = $interval->format('%R');
	}
	if ($beforeSign == '-') { $authorisationType = 'P'; } // La date de fin de réservation est antérieure à la date de début de période autorisée

	if ($authorisationType != 'O') { return ($authorisationType); }

	$afterSign = '+';

	if ($bookingPeriod->getAfter()) {
		$interval = $beginningDate->diff($bookingPeriod->getLastAllowedBookingDate());
		$afterSign = $interval->format('%R');
	}
	if ($afterSign == '-') { $authorisationType = 'P'; } // La date de début de réservation est postérieure à la date de fin de période autorisée

	return ($authorisationType);
	}
}
