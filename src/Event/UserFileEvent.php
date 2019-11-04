<?php
namespace App\Event;

use App\Entity\File;
use App\Entity\UserFile;
use App\Entity\UserFileGroup;

use App\Api\AdministrationApi;
use App\Api\UserFileApi;

class UserFileEvent
{
  static function postPersist($em, \App\Entity\User $user, \App\Entity\UserFile $userFile)
  {
    UserFileEvent::addUserFileToAllUserGroup($em, $user, $userFile);
  }

  // Rattache l'utilisateur au groupe de tous les utilisateurs
  static function addUserFileToAllUserGroup($em, \App\Entity\User $user, \App\Entity\UserFile $userFile)
  {
  // On récupère l'ID du dossier en cours
	$currentFileID = AdministrationApi::getCurrentFileID($em, $user);
  $fRepository = $em->getRepository(File::class);

  // Groupe de tous les utilisateurs
	$userFileGroup = UserFileApi::getAllUserGroup($em, $fRepository->find($currentFileID));

	$userFileGroup->addUserFile($userFile);
  $em->persist($userFileGroup);
  $em->flush();
  }
}
