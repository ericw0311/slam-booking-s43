<?php
namespace App\Api;

use Psr\Log\LoggerInterface;

use App\Entity\File;
use App\Entity\UserFile;
use App\Entity\UserFileGroup;
use App\Entity\UserContext;
use App\Entity\SelectedEntity;
use App\Entity\AddEntity;

class UserFileApi
{
    // Gestion des utilisateurs des réservations
    // Retourne un tableau des utilisateurs sélectionnés
    // resourceIDList: Liste des ID des utilisateurs sélectionnés
    // sortLink: Booléen: Gère-t-on le tri avant et après pour les utilisateurs sélectionnés
    public static function getSelectedUserFiles($em, $userFileIDList, $sortLink)
    {
        $userFileIDArray = explode('-', $userFileIDList);
        $ufRepository = $em->getRepository(UserFile::class);
        $selectedUserFiles = array();
        $i = 0;
        foreach ($userFileIDArray as $userFileID) {
            $userFileDB = $ufRepository->find($userFileID);
            if ($userFileDB !== null) {
                $userFile = new SelectedEntity(); // classe générique des entités sélectionnées
                $userFile->setId($userFileDB->getId());
                $userFile->setName($userFileDB->getFirstAndLastName());
                $userFile->setImageName($userFileDB->getAdministrator() ? "administrator-32.png" : "user-32.png");
                $userFileIDArray_tprr = $userFileIDArray;
                unset($userFileIDArray_tprr[$i]);
                $userFile->setEntityIDList_unselect(implode('-', $userFileIDArray_tprr)); // Liste des utilisateurs sélectionnés si l'utilisateur désélectionne l'utilisateur
                if ($sortLink and (count($userFileIDArray) > 1)) {
                    if ($i > 0) {
                        $userFileIDArray_tprr = $userFileIDArray;
                        $userFileIDArray_tprr[$i] = $userFileIDArray_tprr[$i-1];
                        $userFileIDArray_tprr[$i-1] = $userFileID;
                        $userFile->setEntityIDList_sortBefore(implode('-', $userFileIDArray_tprr)); // Liste des utilisateurs sélectionnés si l'utilisateur remonte l'utilisateur dans l'ordre de tri
                    }
                    if ($i < count($userFileIDArray)-1) {
                        $userFileIDArray_tprr = $userFileIDArray;
                        $userFileIDArray_tprr[$i] = $userFileIDArray_tprr[$i+1];
                        $userFileIDArray_tprr[$i+1] = $userFileID;
                        $userFile->setEntityIDList_sortAfter(implode('-', $userFileIDArray_tprr)); // Liste des utilisateurs sélectionnés si l'utilisateur redescend l'utilisateur dans l'ordre de tri
                    }
                }
                $i++;
                array_push($selectedUserFiles, $userFile);
            }
        }
        return $selectedUserFiles;
    }

    // Retourne un tableau des utilisateurs pouvant être ajoutés à une réservation
    public static function getAvailableUserFiles($userFilesDB, $selectedUserFileIDList)
    {
        $selectedUserFileIDArray = explode('-', $selectedUserFileIDList);
        $availableUserFiles = array();
        foreach ($userFilesDB as $userFileDB) {
            if (array_search($userFileDB->getId(), $selectedUserFileIDArray) === false) {
                $userFile = new AddEntity(); // classe générique des entités pouvant être ajoutées à la sélection
                $userFile->setId($userFileDB->getId());
                $userFile->setName($userFileDB->getFirstAndLastName());
                $userFile->setImageName($userFileDB->getAdministrator() ? "administrator-32.png" : "user-32.png");
                $userFile->setEntityIDList_select(($selectedUserFileIDList == '') ? $userFileDB->getId() : ($selectedUserFileIDList.'-'.$userFileDB->getId())); // Liste des utilisateurs sélectionnés si l'utilisateur sélectionne l'utilisateur
                array_push($availableUserFiles, $userFile);
            }
        }
        return $availableUserFiles;
    }

    // Retourne un tableau d'utilisateurs à partir d'une liste d'ID
    public static function getUserFiles($em, $userFileIDList)
    {
        $userFileIDArray = explode("-", $userFileIDList);
        $userFiles = array();
        $ufRepository = $em->getRepository(UserFile::class);
        foreach ($userFileIDArray as $userFileID) {
            $userFile = $ufRepository->find($userFileID);
            if ($userFile !== null) {
                $userFiles[] = $userFile;
            }
        }
        return $userFiles;
    }

    // Retourne un tableau des ressources à planifier (initialisation de planification)
    public static function initAvailableUserFiles($em, \App\Entity\File $file, $selectedUserFileIDList)
    {
        $ufRepository = $em->getRepository(UserFile::class);
        $userFilesDB = $ufRepository->getUserFiles($file);
        return UserFileApi::getAvailableUserFiles($userFilesDB, $selectedUserFileIDList);
    }

    // Retourne le groupe de tous les utilisateurs d'un dossier
    public static function getAllUserGroup($em, \App\Entity\File $file)
    {
        $ufgRepository = $em->getRepository(UserFileGroup::class);
        return $ufgRepository->findOneBy(array('file' => $file, 'type' => 'ALL'));
    }
}
