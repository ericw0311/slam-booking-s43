<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="uk_planification_line",columns={"planification_period_id", "week_day"})})
 * @ORM\Entity(repositoryClass="App\Repository\PlanificationLineRepository")
 */
class PlanificationLine
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PlanificationPeriod", inversedBy="planificationLines")
     * @ORM\JoinColumn(nullable=false)
     */
    private $planificationPeriod;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $weekDay;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Timetable", inversedBy="planificationLines")
     */
    private $timetable;

    /**
     * @ORM\Column(type="smallint")
     */
    private $oorder;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="planificationLines")
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
     * @ORM\OneToMany(targetEntity="App\Entity\BookingLine", mappedBy="planificationLine")
     */
    private $bookingLines;

    public function __construct(?User $user, ?PlanificationPeriod $planificationPeriod, $weekDay, $order)
    {
  		$this->setUser($user);
  		$this->setPlanificationPeriod($planificationPeriod);
  		$this->setWeekDay($weekDay);
  		$this->setOrder($order);
  		$this->setActive(0);
      $this->bookingLines = new ArrayCollection();
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

    public function getWeekDay(): ?string
    {
        return $this->weekDay;
    }

    public function setWeekDay(string $weekDay): self
    {
        $this->weekDay = $weekDay;

        return $this;
    }

    public function getTimetable(): ?Timetable
    {
        return $this->timetable;
    }

    public function setTimetable(?Timetable $timetable): self
    {
        $this->timetable = $timetable;

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

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

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
            $bookingLine->setPlanificationLine($this);
        }

        return $this;
    }

    public function removeBookingLine(BookingLine $bookingLine): self
    {
        if ($this->bookingLines->contains($bookingLine)) {
            $this->bookingLines->removeElement($bookingLine);
            // set the owning side to null (unless already changed)
            if ($bookingLine->getPlanificationLine() === $this) {
                $bookingLine->setPlanificationLine(null);
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
