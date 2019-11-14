<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
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
     * @ORM\ManyToOne(targetEntity="App\Entity\PlanificationView", inversedBy="planificationViewResources")
     * @ORM\JoinColumn(nullable=false)
     */
    private $planificationView;

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

    public function getPlanificationView(): ?PlanificationView
    {
        return $this->planificationView;
    }

    public function setPlanificationView(?PlanificationView $planificationView): self
    {
        $this->planificationView = $planificationView;
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

    public function __construct(?PlanificationView $planificationView, ?PlanificationResource $planificationResource)
  	{
  	$this->setPlanificationView($planificationView);
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
