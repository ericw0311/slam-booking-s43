<?php
namespace App\Event;

use App\Entity\User;
use App\Entity\UserFile;

use App\Api\AdministrationApi;

class UserEvent
{
    static function postPersist($em, \App\Entity\User $user)
    {
	$atLeastOneFile = UserEvent::updateUserFileFromEmail($em, $user);
    if ($atLeastOneFile) { // Si l'utilisateur enregistre est rattache a un dossier ou plus, on lui positionne un dossier en cours
		AdministrationApi::setFirstFileAsCurrent($em, $user);
    }
    }

    static function postUpdate($em, \App\Entity\User $user)
    {
	UserEvent::updateUserFileFromAccount($em, $user);
    }

    // Met a jour les utilisateurs dossiers correspondants a l'utilisateur inscrit
    // Retourne Vrai si au moins un dossier trouve
	static function updateUserFileFromEmail($em, \App\Entity\User $user)
	{
	$ufRepository = $em->getRepository(UserFile::Class);
	$listUserFile = $ufRepository->findBy(array('email' => $user->getEmail()));
	$atLeastOneFile = false;
	foreach ($listUserFile as $userFile) {
		$atLeastOneFile = true;
		$userFile->setUserCreated(true);
		$userFile->setAccount($user);
		$userFile->setAccountType($user->getAccountType());
		$userFile->setLastName($user->getLastName());
		$userFile->setFirstName($user->getFirstName());
		$userFile->setUniqueName($user->getUniqueName());
		$userFile->setUserName($user->getUserName());
		$em->persist($userFile);
	}
	$em->flush();
	return $atLeastOneFile;
	}

	// Met a jour les utilisateurs dossiers correspondants a l'utilisateur modifie
    static function updateUserFileFromAccount($em, \App\Entity\User $user)
    {
	$ufRepository = $em->getRepository(UserFile::Class);
    $listUserFile = $ufRepository->findBy(array('account' => $user));
    foreach ($listUserFile as $userFile) {
        $userFile->setUserCreated(true);
        $userFile->setAccountType($user->getAccountType());
        $userFile->setLastName($user->getLastName());
        $userFile->setFirstName($user->getFirstName());
        $userFile->setUniqueName($user->getUniqueName());
        $userFile->setUserName($user->getUserName());
        $userFile->setEmail($user->getEmail());
        $em->persist($user);
    }
    $em->flush();
    }
}
