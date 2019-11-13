<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
* @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="uk_booking_line",columns={"resource_id", "ddate", "timetable_id", "timetable_line_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\BookingLineRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class BookingLine
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Booking", inversedBy="bookingLines")
     * @ORM\JoinColumn(nullable=false)
     */
    private $booking;

    /**
     * @ORM\Column(type="date")
     */
    private $ddate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Planification", inversedBy="bookingLines")
     * @ORM\JoinColumn(nullable=false)
     */
    private $planification;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PlanificationPeriod", inversedBy="bookingLines")
     * @ORM\JoinColumn(nullable=false)
     */
    private $planificationPeriod;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PlanificationLine", inversedBy="bookingLines")
     * @ORM\JoinColumn(nullable=false)
     */
    private $planificationLine;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Resource", inversedBy="bookingLines")
     * @ORM\JoinColumn(nullable=false)
     */
    private $resource;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Timetable", inversedBy="bookingLines")
     * @ORM\JoinColumn(nullable=false)
     */
    private $timetable;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TimetableLine", inversedBy="bookingLines")
     * @ORM\JoinColumn(nullable=false)
     */
    private $timetableLine;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="bookingLines")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBooking(): ?Booking
    {
        return $this->booking;
    }

    public function setBooking(?Booking $booking): self
    {
        $this->booking = $booking;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->ddate;
    }

    public function setDate(\DateTimeInterface $ddate): self
    {
        $this->ddate = $ddate;

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

    public function getPlanificationPeriod(): ?PlanificationPeriod
    {
        return $this->planificationPeriod;
    }

    public function setPlanificationPeriod(?PlanificationPeriod $planificationPeriod): self
    {
        $this->planificationPeriod = $planificationPeriod;

        return $this;
    }

    public function getPlanificationLine(): ?PlanificationLine
    {
        return $this->planificationLine;
    }

    public function setPlanificationLine(?PlanificationLine $planificationLine): self
    {
        $this->planificationLine = $planificationLine;

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

    public function getTimetable(): ?Timetable
    {
        return $this->timetable;
    }

    public function setTimetable(?Timetable $timetable): self
    {
        $this->timetable = $timetable;

        return $this;
    }

    public function getTimetableLine(): ?TimetableLine
    {
        return $this->timetableLine;
    }

    public function setTimetableLine(?TimetableLine $timetableLine): self
    {
        $this->timetableLine = $timetableLine;

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

    public function __construct(?User $user, ?Booking $booking, ?Resource $resource)
  	{
  	$this->setUser($user);
  	$this->setBooking($booking);
  	$this->setResource($resource);
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
