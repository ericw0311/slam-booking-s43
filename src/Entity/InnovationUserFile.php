<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InnovationUserFileRepository")
 */
class InnovationUserFile
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Innovation", inversedBy="innovationUserFiles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $innovation;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UserFile", inversedBy="innovationUserFiles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userFile;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInnovation(): ?Innovation
    {
        return $this->innovation;
    }

    public function setInnovation(?Innovation $innovation): self
    {
        $this->innovation = $innovation;

        return $this;
    }

    public function getUserFile(): ?UserFile
    {
        return $this->userFile;
    }

    public function setUserFile(?UserFile $userFile): self
    {
        $this->userFile = $userFile;

        return $this;
    }
}
