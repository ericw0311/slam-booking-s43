<?php
namespace App\Event;

use App\Entity\File;
use App\Entity\UserFile;
use App\Entity\UserParameter;
use App\Entity\Timetable;
use App\Entity\TimetableLine;
use App\Entity\UserFileGroup;

use App\Api\AdministrationApi;

class FileEvent
{
    static function postPersist($em, \App\Entity\User $user, \App\Entity\File $file, $translator)
    {
      AdministrationApi::setCurrentFileIfNotDefined($em, $user, $file);
      FileEvent::createTimetables($em, $user, $file, $translator);
      FileEvent::createUserFileGroup($em, $user, $file, $translator);
      FileEvent::createUserFile($em, $user, $file);
    }

    // Rattache l'utilisateur courant au dossier
    static function createUserFile($em, \App\Entity\User $user, \App\Entity\File $file)
    {
    $userFile = new UserFile($user, $file);
    $userFile->setAccount($user);
    $userFile->setEmail($user->getEmail());
    $userFile->setAccountType($user->getAccountType());
    $userFile->setLastName($user->getLastName());
    $userFile->setFirstName($user->getFirstName());
    $userFile->setUniqueName($user->getUniqueName());
    $userFile->setAdministrator(1); // Le createur du dossier est administrateur.
    $userFile->setUserCreated(1);
    $userFile->setUsername($user->getUsername());
    $userFile->setResourceUser(0);

    $em->persist($userFile);
    $em->flush();
    }


	// Crée les grilles horaires D = journée et HD = demi journée
	static function createTimetables($em, \App\Entity\User $user, \App\Entity\File $file, $translator)
	{
	$timetable = new Timetable($user, $file);
	$timetable->setType("D");
	$timetable->setName($translator->trans('timetable.day'));
	$em->persist($timetable);

	$timetableLine = new TimetableLine($user, $timetable);
	$timetableLine->setType("D");
	$timetableLine->setBeginningTime(date_create_from_format('H:i:s','00:00:00'));
	$timetableLine->setEndTime(date_create_from_format('H:i:s','23:59:00'));
	$em->persist($timetableLine);
	$timetable = new Timetable($user, $file);
	$timetable->setType("HD");
	$timetable->setName($translator->trans('timetable.half.day'));
	$em->persist($timetable);

	$timetableLine = new TimetableLine($user, $timetable);
	$timetableLine->setType("AM");
	$timetableLine->setBeginningTime(date_create_from_format('H:i:s','00:00:00'));
	$timetableLine->setEndTime(date_create_from_format('H:i:s','12:00:00'));
	$em->persist($timetableLine);
	$timetableLine = new TimetableLine($user, $timetable);
	$timetableLine->setType("PM");
	$timetableLine->setBeginningTime(date_create_from_format('H:i:s','12:00:00'));
	$timetableLine->setEndTime(date_create_from_format('H:i:s','23:59:00'));
	$em->persist($timetableLine);
	$em->flush();
	}

  // Crée le groupe d'utilisateurs: Tous les utilisateurs
	static function createUserFileGroup($em, \App\Entity\User $user, \App\Entity\File $file, $translator)
	{
	$userFileGroup = new UserFileGroup($user, $file, "ALL");
	$userFileGroup->setName($translator->trans('userFileGroup.all'));
	$em->persist($userFileGroup);
	$em->flush();
	}
}
