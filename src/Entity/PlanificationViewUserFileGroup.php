<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="uk_planification_view_user_file_group",columns={"planification_period_id", "user_file_group_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\PlanificationViewUserFileGroupRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class PlanificationViewUserFileGroup
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PlanificationPeriod", inversedBy="planificationViewUserFileGroups")
     * @ORM\JoinColumn(nullable=false)
     */
    private $planificationPeriod;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UserFileGroup", inversedBy="planificationViewUserFileGroups")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userFileGroup;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\Column(type="smallint")
     */
    private $oorder;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="planificationViewUserFileGroups")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

  	/**
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PlanificationViewResource", mappedBy="planificationViewUserFileGroup", orphanRemoval=true)
     */
    private $planificationViewResources;

    public function __construct(?User $user, ?PlanificationPeriod $planificationPeriod, ?UserFileGroup $userFileGroup)
    {
      $this->setUser($user);
      $this->setPlanificationPeriod($planificationPeriod);
      $this->setUserFileGroup($userFileGroup);
      $this->setActive(true);
      $this->setOrder(1);
      $this->planificationViewResources = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlanificationPeriod(): ?PlanificationPeriod
    {
        return $this->planificationPeriod;
    }

    public function setPlanificationPeriod(?PlanificationPeriod $planificationPeriod): self
    {
        $this->planificationPeriod = $planificationPeriod;

        return $this;
    }

    public function getUserFileGroup(): ?UserFileGroup
    {
        return $this->userFileGroup;
    }

    public function setUserFileGroup(?UserFileGroup $userFileGroup): self
    {
        $this->userFileGroup = $userFileGroup;
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

    public function getOrder(): ?int
    {
        return $this->oorder;
    }

    public function setOrder(int $oorder): self
    {
        $this->oorder = $oorder;
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

    /**
     * @return Collection|PlanificationViewResource[]
     */
    public function getPlanificationViewResources(): Collection
    {
        return $this->planificationViewResources;
    }

    public function addPlanificationViewResource(PlanificationViewResource $planificationViewResource): self
    {
        if (!$this->planificationViewResources->contains($planificationViewResource)) {
            $this->planificationViewResources[] = $planificationViewResource;
            $planificationViewResource->setPlanificationViewUserFileGroup($this);
        }
        return $this;
    }

    public function removePlanificationViewResource(PlanificationViewResource $planificationViewResource): self
    {
        if ($this->planificationViewResources->contains($planificationViewResource)) {
            $this->planificationViewResources->removeElement($planificationViewResource);
            // set the owning side to null (unless already changed)
            if ($planificationViewResource->getPlanificationViewUserFileGroup() === $this) {
                $planificationViewResource->setPlanificationViewUserFileGroup(null);
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
