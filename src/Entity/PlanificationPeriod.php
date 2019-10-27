<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="uk_planification_period",columns={"planification_id", "beginning_date"})})
 * @ORM\Entity(repositoryClass="App\Repository\PlanificationPeriodRepository")
 */
class PlanificationPeriod
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $beginningDate;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $endDate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Planification", inversedBy="planificationPeriods")
     * @ORM\JoinColumn(nullable=false)
     */
    private $planification;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="planificationPeriods")
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
     * @ORM\OneToMany(targetEntity="App\Entity\PlanificationLine", mappedBy="planificationPeriod", orphanRemoval=true)
     */
    private $planificationLines;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PlanificationResource", mappedBy="planificationPeriod", orphanRemoval=true)
     */
    private $planificationResources;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PlanificationView", mappedBy="planificationPeriod", orphanRemoval=true)
     */
    private $planificationViews;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BookingLine", mappedBy="planificationPeriod")
     */
    private $bookingLines;

    public function __construct(?User $user, ?Planification $planification)
    {
        $this->setUser($user);
        $this->setPlanification($planification);
        $this->planificationLines = new ArrayCollection();
        $this->planificationResources = new ArrayCollection();
        $this->planificationViews = new ArrayCollection();
        $this->bookingLines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBeginningDate(): ?\DateTimeInterface
    {
        return $this->beginningDate;
    }

    public function setBeginningDate(?\DateTimeInterface $beginningDate): self
    {
        $this->beginningDate = $beginningDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getPlanification(): ?Planification
    {
        return $this->planification;
    }

    public function setPlanification(?Planification $planification): self
    {
        $this->planification = $planification;

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
     * @return Collection|PlanificationLine[]
     */
    public function getPlanificationLines(): Collection
    {
        return $this->planificationLines;
    }

    public function addPlanificationLine(PlanificationLine $planificationLine): self
    {
        if (!$this->planificationLines->contains($planificationLine)) {
            $this->planificationLines[] = $planificationLine;
            $planificationLine->setPlanificationPeriod($this);
        }

        return $this;
    }

    public function removePlanificationLine(PlanificationLine $planificationLine): self
    {
        if ($this->planificationLines->contains($planificationLine)) {
            $this->planificationLines->removeElement($planificationLine);
            // set the owning side to null (unless already changed)
            if ($planificationLine->getPlanificationPeriod() === $this) {
                $planificationLine->setPlanificationPeriod(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PlanificationResource[]
     */
    public function getPlanificationResources(): Collection
    {
        return $this->planificationResources;
    }

    public function addPlanificationResource(PlanificationResource $planificationResource): self
    {
        if (!$this->planificationResources->contains($planificationResource)) {
            $this->planificationResources[] = $planificationResource;
            $planificationResource->setPlanificationPeriod($this);
        }

        return $this;
    }

    public function removePlanificationResource(PlanificationResource $planificationResource): self
    {
        if ($this->planificationResources->contains($planificationResource)) {
            $this->planificationResources->removeElement($planificationResource);
            // set the owning side to null (unless already changed)
            if ($planificationResource->getPlanificationPeriod() === $this) {
                $planificationResource->setPlanificationPeriod(null);
            }
        }

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
            $planificationView->setPlanificationPeriod($this);
        }

        return $this;
    }

    public function removePlanificationView(PlanificationView $planificationView): self
    {
        if ($this->planificationViews->contains($planificationView)) {
            $this->planificationViews->removeElement($planificationView);
            // set the owning side to null (unless already changed)
            if ($planificationView->getPlanificationPeriod() === $this) {
                $planificationView->setPlanificationPeriod(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|BookingLine[]
     */
    public function getBookingLines(): Collection
    {
        return $this->bookingLines;
    }

    public function addBookingLine(BookingLine $bookingLine): self
    {
        if (!$this->bookingLines->contains($bookingLine)) {
            $this->bookingLines[] = $bookingLine;
            $bookingLine->setPlanificationPeriod($this);
        }

        return $this;
    }

    public function removeBookingLine(BookingLine $bookingLine): self
    {
        if ($this->bookingLines->contains($bookingLine)) {
            $this->bookingLines->removeElement($bookingLine);
            // set the owning side to null (unless already changed)
            if ($bookingLine->getPlanificationPeriod() === $this) {
                $bookingLine->setPlanificationPeriod(null);
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
