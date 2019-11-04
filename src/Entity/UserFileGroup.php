<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
* @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="uk_user_file_group",columns={"file_id", "name"})})
* @ORM\Entity(repositoryClass="App\Repository\UserFileGroupRepository")
* @ORM\HasLifecycleCallbacks()
* @UniqueEntity(fields={"file", "name"}, errorPath="name", message="userFileGroup.already.exists")
*/
class UserFileGroup
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Choice({"ALL", "MANUAL"})
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\UserFile", inversedBy="userFileGroups")
     */
    private $userFiles;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="userFileGroups")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\File", inversedBy="userFileGroups")
     * @ORM\JoinColumn(nullable=false)
     */
    private $file;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

  	/**
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PlanificationView", mappedBy="userFileGroup", orphanRemoval=true)
     */
    private $planificationViews;

    public function __construct(?User $user, ?File $file, string $type)
    {
  		$this->setUser($user);
  		$this->setFile($file);
      $this->setType($type);
      $this->userFiles = new ArrayCollection();
      $this->planificationViews = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|UserFile[]
     */
    public function getUserFiles(): Collection
    {
        return $this->userFiles;
    }

    public function addUserFile(UserFile $userFile): self
    {
        if (!$this->userFiles->contains($userFile)) {
            $this->userFiles[] = $userFile;
        }

        return $this;
    }

    public function removeUserFile(UserFile $userFile): self
    {
        if ($this->userFiles->contains($userFile)) {
            $this->userFiles->removeElement($userFile);
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return Collection|PlanificationView[]
     */
    public function getPlanificationViews(): Collection
    {
        return $this->planificationViews;
    }

    public function addPlanificationView(PlanificationView $planificationView): self
    {
        if (!$this->planificationViews->contains($planificationView)) {
            $this->planificationViews[] = $planificationView;
            $planificationView->setUserFileGroup($this);
        }

        return $this;
    }

    public function removePlanificationView(PlanificationView $planificationView): self
    {
        if ($this->planificationViews->contains($planificationView)) {
            $this->planificationViews->removeElement($planificationView);
            // set the owning side to null (unless already changed)
            if ($planificationView->getUserFileGroup() === $this) {
                $planificationView->setUserFileGroup(null);
            }
        }

        return $this;
    }

    /**
    * @ORM\PrePersist
    */
    public function createDate()
    {
      $this->createdAt = new \DateTime();
    }

    /**
    * @ORM\PreUpdate
    */
    public function updateDate()
    {
      $this->updatedAt = new \DateTime();
    }
}
