<?php
namespace App\Entity;

use App\Api\AdministrationApi;

class UserContext
{
  protected $user;
  protected $currentFile;
  protected $currentUserFile;

  public function setUser($user)
    {
    $this->user = $user;
    return $this;
    }

    public function getUser()
    {
    return $this->user;
    }

    public function setCurrentFile($currentFile)
    {
    $this->currentFile = $currentFile;
    return $this;
    }

    public function getCurrentFile()
    {
    return $this->currentFile;
    }

    public function getCurrentUserFile()
    {
    return $this->currentUserFile;
    }

    public function getCurrentUserFileAdministrator()
    {
    if ($this->currentUserFile != null) {
        return $this->currentUserFile->getAdministrator();
    } else {
        return false;
    }
    }

    function __construct($em, \App\Entity\User $user)
    {
    $this->user = $user;
    if ($user != null) { // Peut etre appele sans utilisateur connecte (home page)
        // Recherche du dossier en cours
        $currentFileID = AdministrationApi::getCurrentFileID($em, $user);
        $fRepository = $em->getRepository(File::class);
        $this->currentFile = $fRepository->find($currentFileID);

        $ufRepository = $em->getRepository(UserFile::class);
        $this->currentUserFile = $ufRepository->findOneBy(array('account' => $this->user, 'file' => $this->currentFile));
    }
    return $this;
    }

    public function getCurrentFileID()
    {
    if (null === $this->currentFile) {
        return 0;
    }
    return $this->getCurrentFile()->getID();
    }

    // Retourne le nom du dossier en cours ou "Slam Booking" si aucun dossier en cours
    public function getCurrentFileName()
    {
    if (null === $this->currentFile) {
        return "Slam Booking";
    }
    return $this->getCurrentFile()->getName();
    }
}
