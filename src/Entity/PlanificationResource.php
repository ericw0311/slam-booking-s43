<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="uk_planification_resource",columns={"planification_period_id", "resource_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\PlanificationResourceRepository")
 */
class PlanificationResource
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PlanificationPeriod", inversedBy="planificationResources")
     * @ORM\JoinColumn(nullable=false)
     */
    private $planificationPeriod;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Resource", inversedBy="planificationResources")
     * @ORM\JoinColumn(nullable=false)
     */
    private $resource;

    /**
     * @ORM\Column(type="smallint")
     */
    private $oorder;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="planificationResources")
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
     * @ORM\OneToMany(targetEntity="App\Entity\PlanificationViewResource", mappedBy="planificationResource", orphanRemoval=true)
     */
    private $planificationViewResources;

    public function __construct(?User $user, ?PlanificationPeriod $planificationPeriod, ?Resource $resource)
    {
      $this->setUser($user);
      $this->setPlanificationPeriod($planificationPeriod);
      $this->setResource($resource);
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

    public function getResource(): ?Resource
    {
        return $this->resource;
    }

    public function setResource(?Resource $resource): self
    {
        $this->resource = $resource;

        return $this;
    }

    public function getOorder(): ?int
    {
        return $this->oorder;
    }

    public function setOorder(int $oorder): self
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
            $planificationViewResource->setPlanificationResource($this);
        }

        return $this;
    }

    public function removePlanificationViewResource(PlanificationViewResource $planificationViewResource): self
    {
        if ($this->planificationViewResources->contains($planificationViewResource)) {
            $this->planificationViewResources->removeElement($planificationViewResource);
            // set the owning side to null (unless already changed)
            if ($planificationViewResource->getPlanificationResource() === $this) {
                $planificationViewResource->setPlanificationResource(null);
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
