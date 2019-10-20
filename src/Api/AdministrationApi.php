<?php
// src/Api/AdministrationApi.php
namespace App\Api;

use App\Entity\File;
use App\Entity\FileParameter;
use App\Entity\UserParameter;
use App\Entity\Constants;

class AdministrationApi
{
	// Retourne le dossier en cours d'un utilisateur
	static function getCurrentFile($em, \App\Entity\User $user)
	{
	$upRepository = $em->getRepository(UserParameter::class);
	return $upRepository->findOneBy(array('user' => $user, 'parameterGroup' => 'booking', 'parameter' => 'current.file'));
	}

	// Retourne l'ID du dossier en cours d'un utilisateur
	static function getCurrentFileID($em, \App\Entity\User $user)
	{
	$upRepository = $em->getRepository(UserParameter::class);
	$userParameter = $upRepository->findOneBy(array('user' => $user, 'parameterGroup' => 'booking', 'parameter' => 'current.file'));
	if ($userParameter === null) {
		return 0;
	} else {
		return $userParameter->getIntegerValue();
	}
	}

	// Positionne le dossier comme dossier en cours
	static function setCurrentFile($em, \App\Entity\User $user, \App\Entity\File $file)
	{
	// Recherche du dossier en cours
	$userParameter = AdministrationApi::getCurrentFile($em, $user);
	if ($userParameter === null) {
		$userParameter = new UserParameter($user, 'booking', 'current.file');
		$userParameter->setSBIntegerValue($file->getId());
		$em->persist($userParameter);
	} else {
		$userParameter->setSBIntegerValue($file->getId());
	}
	$em->flush();
	}

	// Positionne le dossier comme dossier en cours (idem setCurrentFile mais directement à partir de l'ID du dossier)
	static function setCurrentFileID($em, \App\Entity\User $user, $fileID)
	{
	// Recherche du dossier en cours
	$userParameter = AdministrationApi::getCurrentFile($em, $user);
	if ($userParameter === null) {
		$userParameter = new UserParameter($user, 'booking', 'current.file');
		$userParameter->setSBIntegerValue($fileID);
		$em->persist($userParameter);
	} else {
		$userParameter->setSBIntegerValue($fileID);
	}
	$em->flush();
	}

	// Positionne le dossier comme dossier en cours si l'utilisateur n'a pas de dossier en cours
	static function setCurrentFileIfNotDefined($em, \App\Entity\User $user, \App\Entity\File $file)
	{
	// Recherche du dossier en cours
	$userParameter = AdministrationApi::getCurrentFile($em, $user);
	if ($userParameter === null) {
		$userParameter = new UserParameter($user, 'booking', 'current.file');
		$userParameter->setSBIntegerValue($file->getId());
		$em->persist($userParameter);
		$em->flush();
	}
	}

	// Positionne le premier dossier comme dossier en cours
	static function setFirstFileAsCurrent($em, \App\Entity\User $user)
	{
	// Recherche du dossier en cours
	$userParameter = AdministrationApi::getCurrentFile($em, $user);
	// Recherche du premier dossier de l'utilisateur
	$fRepository = $em->getRepository(File::class);
	$firstFile = $fRepository->getUserFirstFile($user);

	$doFlush = false;
	if ($firstFile != null) { // Le premier dossier est trouve
		if ($userParameter === null) { // Mise a jour du parametre "dossier en cours"
			$userParameter = new UserParameter($user, 'booking', 'current.file');
			$userParameter->setSBIntegerValue($firstFile->getId());
			$em->persist($userParameter);
		} else { // Creation "dossier en cours"
			$userParameter->setSBIntegerValue($firstFile->getId());
		}
		$doFlush = true;
	} else { // Plus de dossier: suppression du parametre
		if ($userParameter != null) {
			$em->remove($userParameter);
			$doFlush = true;
		}
	}
	if ($doFlush) {
		$em->flush();
	}
	}

	// Retourne le nombre de lignes affichées dans les listes pour une entité donnée
	static function getNumberLines($em, \App\Entity\User $user, $entityCode)
	{
	$upRepository = $em->getRepository(UserParameter::Class);

	$userParameter = $upRepository->findOneBy(array('user' => $user, 'parameterGroup' => ($entityCode.'.number.lines.columns'), 'parameter' => ($entityCode.'.number.lines')));
	if ($userParameter != null) { $numberLines = $userParameter->getIntegerValue(); } else { $numberLines =  constant(Constants::class.'::LIST_DEFAULT_NUMBER_LINES'); }

	return $numberLines;
	}

	// Met à jour le nombre de lignes affichées dans les listes pour une entité donnée
	static function setNumberLines($em, \App\Entity\User $user, $entityCode, $numberLines)
	{
	$upRepository = $em->getRepository(UserParameter::Class);

	$userParameter = $upRepository->findOneBy(array('user' => $user, 'parameterGroup' => ($entityCode.'.number.lines.columns'), 'parameter' => ($entityCode.'.number.lines')));
	if ($userParameter != null) {
		$userParameter->setSBIntegerValue($numberLines);
	} else {
		$userParameter = new UserParameter($user, $entityCode.'.number.lines.columns', $entityCode.'.number.lines');
		$userParameter->setSBIntegerValue($numberLines);
		$em->persist($userParameter);
	}
	$em->flush();
	}

	// Retourne le nombre de colonnes affichées dans les listes pour une entité donnée
	static function getNumberColumns($em, \App\Entity\User $user, $entityCode)
	{
	$upRepository = $em->getRepository(UserParameter::Class);

	$userParameter = $upRepository->findOneBy(array('user' => $user, 'parameterGroup' => ($entityCode.'.number.lines.columns'), 'parameter' => ($entityCode.'.number.columns')));
	if ($userParameter != null) { $numberColumns = $userParameter->getIntegerValue(); } else { $numberColumns =  constant(Constants::class.'::LIST_DEFAULT_NUMBER_LINES'); }

	return $numberColumns;
	}

	// Met à jour le nombre de colonnes affichées dans les listes pour une entité donnée
	static function setNumberColumns($em, \App\Entity\User $user, $entityCode, $numberColumns)
	{
	$upRepository = $em->getRepository(UserParameter::Class);

	$userParameter = $upRepository->findOneBy(array('user' => $user, 'parameterGroup' => ($entityCode.'.number.lines.columns'), 'parameter' => ($entityCode.'.number.columns')));
	if ($userParameter != null) {
		$userParameter->setSBIntegerValue($numberColumns);
	} else {
		$userParameter = new UserParameter($user, $entityCode.'.number.lines.columns', $entityCode.'.number.columns');
		$userParameter->setSBIntegerValue($numberColumns);
		$em->persist($userParameter);
	}
	$em->flush();
	}

	// Met à jour l'indicateur: envoi de mails aux administrateurs du dossier à la saisie/MAJ/suppression des réservations.
	static function setFileBookingEmailAdministrator($em, \App\Entity\User $user, \App\Entity\File $file, $fileAdministrator)
	{
	$fpRepository = $em->getRepository(FileParameter::class);

	$fileParameter = $fpRepository->findOneBy(array('file' => $file, 'parameterGroup' => 'booking.mail', 'parameter' => 'administrator'));
	if ($fileParameter != null) {
		$fileParameter->setSBBooleanValue($fileAdministrator);
	} else {
		$fileParameter = new FileParameter($user, $file, 'booking.mail', 'administrator');
		$fileParameter->setSBBooleanValue($fileAdministrator);
		$em->persist($fileParameter);
	}
	$em->flush();
	}

	// Retourne l'indicateur: envoi de mails aux administrateurs du dossier à la saisie/MAJ/suppression des réservations.
	static function getFileBookingEmailAdministrator($em, \App\Entity\File $file)
	{
	$fpRepository = $em->getRepository(FileParameter::class);

	$fileParameter = $fpRepository->findOneBy(array('file' => $file, 'parameterGroup' => 'booking.mail', 'parameter' => 'administrator'));
	if ($fileParameter != null) { $fileAdministrator = $fileParameter->getBooleanValue(); } else { $fileAdministrator =  constant(Constants::class.'::BOOKING_MAIL_ADMINISTRATOR'); }

	return $fileAdministrator;
	}

	// Met à jour l'indicateur: envoi de mails aux utilisateurs à la saisie/MAJ/suppression des réservations.
	static function setFileBookingEmailUser($em, \App\Entity\User $user, \App\Entity\File $file, $bookingUser)
	{
	$fpRepository = $em->getRepository(FileParameter::class);

	$fileParameter = $fpRepository->findOneBy(array('file' => $file, 'parameterGroup' => 'booking.mail', 'parameter' => 'user'));
	if ($fileParameter != null) {
		$fileParameter->setSBBooleanValue($bookingUser);
	} else {
		$fileParameter = new FileParameter($user, $file, 'booking.mail', 'user');
		$fileParameter->setSBBooleanValue($bookingUser);
		$em->persist($fileParameter);
	}
	$em->flush();
	}

	// Retourne l'indicateur: envoi de mails aux utilisateurs à la saisie/MAJ/suppression des réservations.
	static function getFileBookingEmailUser($em, \App\Entity\File $file)
	{
	$fpRepository = $em->getRepository(FileParameter::class);

	$fileParameter = $fpRepository->findOneBy(array('file' => $file, 'parameterGroup' => 'booking.mail', 'parameter' => 'user'));
	if ($fileParameter != null) { $bookingUser = $fileParameter->getBooleanValue(); } else { $bookingUser =  constant(Constants::class.'::BOOKING_MAIL_USER'); }

	return $bookingUser;
	}

	// Met à jour l'indicateur: Restriction de période de réservation avant la date du jour.
	static function setFileBookingPeriodBefore($em, \App\Entity\User $user, \App\Entity\File $file, $before)
	{
	$fpRepository = $em->getRepository(FileParameter::class);

	$fileParameter = $fpRepository->findOneBy(array('file' => $file, 'parameterGroup' => 'booking.period', 'parameter' => 'before'));
	if ($fileParameter != null) {
		$fileParameter->setSBBooleanValue($before);
	} else {
		$fileParameter = new FileParameter($user, $file, 'booking.period', 'before');
		$fileParameter->setSBBooleanValue($before);
		$em->persist($fileParameter);
	}
	$em->flush();
	}

	// Retourne l'indicateur: Restriction de période de réservation avant la date du jour.
	static function getFileBookingPeriodBefore($em, \App\Entity\File $file)
	{
	$fpRepository = $em->getRepository(FileParameter::class);

	$fileParameter = $fpRepository->findOneBy(array('file' => $file, 'parameterGroup' => 'booking.period', 'parameter' => 'before'));
	if ($fileParameter != null) { $before = $fileParameter->getBooleanValue(); } else { $before =  constant(Constants::class.'::BOOKING_PERIOD_BEFORE'); }

	return $before;
	}

	// Met à jour le type de restriction de période de réservation avant la date du jour.
	static function setFileBookingPeriodBeforeType($em, \App\Entity\User $user, \App\Entity\File $file, $beforeType)
	{
	$fpRepository = $em->getRepository(FileParameter::class);

	$fileParameter = $fpRepository->findOneBy(array('file' => $file, 'parameterGroup' => 'booking.period', 'parameter' => 'before.type'));
	if ($fileParameter != null) {
		$fileParameter->setSBStringValue($beforeType);
	} else {
		$fileParameter = new FileParameter($user, $file, 'booking.period', 'before.type');
		$fileParameter->setSBStringValue($beforeType);
		$em->persist($fileParameter);
	}
	$em->flush();
	}

	// Retourne le type de restriction de période de réservation avant la date du jour.
	static function getFileBookingPeriodBeforeType($em, \App\Entity\File $file)
	{
	$fpRepository = $em->getRepository(FileParameter::class);

	$fileParameter = $fpRepository->findOneBy(array('file' => $file, 'parameterGroup' => 'booking.period', 'parameter' => 'before.type'));
	if ($fileParameter != null) { $beforeType = $fileParameter->getStringValue(); } else { $beforeType =  constant(Constants::class.'::BOOKING_PERIOD_BEFORE_TYPE'); }

	return $beforeType;
	}

	// Met à jour le nombre associé à la restriction de période de réservation avant la date du jour.
	static function setFileBookingPeriodBeforeNumber($em, \App\Entity\User $user, \App\Entity\File $file, $beforeNumber)
	{
	$fpRepository = $em->getRepository(FileParameter::class);

	$fileParameter = $fpRepository->findOneBy(array('file' => $file, 'parameterGroup' => 'booking.period', 'parameter' => 'before.number'));
	if ($fileParameter != null) {
		$fileParameter->setSBIntegerValue($beforeNumber);
	} else {
		$fileParameter = new FileParameter($user, $file, 'booking.period', 'before.number');
		$fileParameter->setSBIntegerValue($beforeNumber);
		$em->persist($fileParameter);
	}
	$em->flush();
	}

	// Retourne le nombre associé à la restriction de période de réservation avant la date du jour.
	static function getFileBookingPeriodBeforeNumber($em, \App\Entity\File $file)
	{
	$fpRepository = $em->getRepository(FileParameter::class);

	$fileParameter = $fpRepository->findOneBy(array('file' => $file, 'parameterGroup' => 'booking.period', 'parameter' => 'before.number'));
	if ($fileParameter != null) { $beforeNumber = $fileParameter->getIntegerValue(); } else { $beforeNumber =  constant(Constants::class.'::BOOKING_PERIOD_BEFORE_NUMBER'); }

	return $beforeNumber;
	}

	// Met à jour l'indicateur: Restriction de période de réservation après la date du jour.
	static function setFileBookingPeriodAfter($em, \App\Entity\User $user, \App\Entity\File $file, $after)
	{
	$fpRepository = $em->getRepository(FileParameter::class);

	$fileParameter = $fpRepository->findOneBy(array('file' => $file, 'parameterGroup' => 'booking.period', 'parameter' => 'after'));
	if ($fileParameter != null) {
		$fileParameter->setSBBooleanValue($after);
	} else {
		$fileParameter = new FileParameter($user, $file, 'booking.period', 'after');
		$fileParameter->setSBBooleanValue($after);
		$em->persist($fileParameter);
	}
	$em->flush();
	}

	// Retourne l'indicateur: Restriction de période de réservation après la date du jour.
	static function getFileBookingPeriodAfter($em, \App\Entity\File $file)
	{
	$fpRepository = $em->getRepository(FileParameter::class);

	$fileParameter = $fpRepository->findOneBy(array('file' => $file, 'parameterGroup' => 'booking.period', 'parameter' => 'after'));
	if ($fileParameter != null) { $after = $fileParameter->getBooleanValue(); } else { $after =  constant(Constants::class.'::BOOKING_PERIOD_AFTER'); }

	return $after;
	}

	// Met à jour le type de restriction de période de réservation après la date du jour.
	static function setFileBookingPeriodAfterType($em, \App\Entity\User $user, \App\Entity\File $file, $afterType)
	{
	$fpRepository = $em->getRepository(FileParameter::class);

	$fileParameter = $fpRepository->findOneBy(array('file' => $file, 'parameterGroup' => 'booking.period', 'parameter' => 'after.type'));
	if ($fileParameter != null) {
		$fileParameter->setSBStringValue($afterType);
	} else {
		$fileParameter = new FileParameter($user, $file, 'booking.period', 'after.type');
		$fileParameter->setSBStringValue($afterType);
		$em->persist($fileParameter);
	}
	$em->flush();
	}

	// Retourne le type de restriction de période de réservation après la date du jour.
	static function getFileBookingPeriodAfterType($em, \App\Entity\File $file)
	{
	$fpRepository = $em->getRepository(FileParameter::class);

	$fileParameter = $fpRepository->findOneBy(array('file' => $file, 'parameterGroup' => 'booking.period', 'parameter' => 'after.type'));
	if ($fileParameter != null) { $afterType = $fileParameter->getStringValue(); } else { $afterType =  constant(Constants::class.'::BOOKING_PERIOD_AFTER_TYPE'); }

	return $afterType;
	}

	// Met à jour le nombre associé à la restriction de période de réservation après la date du jour.
	static function setFileBookingPeriodAfterNumber($em, \App\Entity\User $user, \App\Entity\File $file, $afterNumber)
	{
	$fpRepository = $em->getRepository(FileParameter::class);

	$fileParameter = $fpRepository->findOneBy(array('file' => $file, 'parameterGroup' => 'booking.period', 'parameter' => 'after.number'));
	if ($fileParameter != null) {
		$fileParameter->setSBIntegerValue($afterNumber);
	} else {
		$fileParameter = new FileParameter($user, $file, 'booking.period', 'after.number');
		$fileParameter->setSBIntegerValue($afterNumber);
		$em->persist($fileParameter);
	}
	$em->flush();
	}

	// Retourne le nombre associé à la restriction de période de réservation après la date du jour.
	static function getFileBookingPeriodAfterNumber($em, \App\Entity\File $file)
	{
	$fpRepository = $em->getRepository(FileParameter::class);

	$fileParameter = $fpRepository->findOneBy(array('file' => $file, 'parameterGroup' => 'booking.period', 'parameter' => 'after.number'));
	if ($fileParameter != null) { $afterNumber = $fileParameter->getIntegerValue(); } else { $afterNumber =  constant(Constants::class.'::BOOKING_PERIOD_AFTER_NUMBER'); }

	return $afterNumber;
	}
}
