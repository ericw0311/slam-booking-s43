<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InnovationRepository")
 */
class Innovation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $code;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\Column(type="boolean")
     */
    private $administratorOnly;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\InnovationUserFile", mappedBy="innovation", orphanRemoval=true)
     */
    private $innovationUserFiles;

    public function __construct()
    {
        $this->innovationUserFiles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getAdministratorOnly(): ?bool
    {
        return $this->administratorOnly;
    }

    public function setAdministratorOnly(bool $administratorOnly): self
    {
        $this->administratorOnly = $administratorOnly;

        return $this;
    }

    /**
     * @return Collection|InnovationUserFile[]
     */
    public function getInnovationUserFiles(): Collection
    {
        return $this->innovationUserFiles;
    }

    public function addInnovationUserFile(InnovationUserFile $innovationUserFile): self
    {
        if (!$this->innovationUserFiles->contains($innovationUserFile)) {
            $this->innovationUserFiles[] = $innovationUserFile;
            $innovationUserFile->setInnovation($this);
        }

        return $this;
    }

    public function removeInnovationUserFile(InnovationUserFile $innovationUserFile): self
    {
        if ($this->innovationUserFiles->contains($innovationUserFile)) {
            $this->innovationUserFiles->removeElement($innovationUserFile);
            // set the owning side to null (unless already changed)
            if ($innovationUserFile->getInnovation() === $this) {
                $innovationUserFile->setInnovation(null);
            }
        }

        return $this;
    }
}
