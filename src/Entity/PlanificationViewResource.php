<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="uk_planification_view_resource",columns={"planification_view_user_file_group_id", "planification_resource_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\PlanificationViewResourceRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class PlanificationViewResource
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PlanificationViewUserFileGroup", inversedBy="planificationViewResources")
     * @ORM\JoinColumn(nullable=false)
     */
    private $planificationViewUserFileGroup;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PlanificationResource", inversedBy="planificationViewResources")
     * @ORM\JoinColumn(nullable=false)
     */
    private $planificationResource;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

  	/**
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlanificationViewUserFileGroup(): ?PlanificationViewUserFileGroup
    {
        return $this->planificationViewUserFileGroup;
    }

    public function setPlanificationViewUserFileGroup(?PlanificationViewUserFileGroup $planificationViewUserFileGroup): self
    {
        $this->planificationViewUserFileGroup = $planificationViewUserFileGroup;
        return $this;
    }

    public function getPlanificationResource(): ?PlanificationResource
    {
        return $this->planificationResource;
    }

    public function setPlanificationResource(?PlanificationResource $planificationResource): self
    {
        $this->planificationResource = $planificationResource;
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

    public function __construct(?PlanificationViewUserFileGroup $planificationViewUserFileGroup, ?PlanificationResource $planificationResource)
  	{
  	$this->setPlanificationViewUserFileGroup($planificationViewUserFileGroup);
  	$this->setPlanificationResource($planificationResource);
  	$this->setActive(true);
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
