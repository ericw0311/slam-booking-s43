<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
* @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="uk_planification",columns={"file_id", "type", "name"})})
* @ORM\Entity(repositoryClass="App\Repository\PlanificationRepository")
* @ORM\HasLifecycleCallbacks()
* @UniqueEntity(fields={"file", "type", "name"}, errorPath="name", message="planification.already.exists")
*/
class Planification
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
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $internal;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $code;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="planifications")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\File", inversedBy="planifications")
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
     * @ORM\OneToMany(targetEntity="App\Entity\PlanificationPeriod", mappedBy="planification", orphanRemoval=true)
     */
    private $planificationPeriods;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Booking", mappedBy="planification")
     */
    private $bookings;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BookingLine", mappedBy="planification")
     */
    private $bookingLines;

    public function __construct(?User $user, ?File $file)
    {
     $this->setUser($user);
     $this->setFile($file);
     $this->planificationPeriods = new ArrayCollection();
     $this->bookings = new ArrayCollection();
     $this->bookingLines = new ArrayCollection();
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

    public function getInternal(): ?bool
    {
        return $this->internal;
    }

    public function setInternal(bool $internal): self
    {
        $this->internal = $internal;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

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
     * @return Collection|PlanificationPeriod[]
     */
    public function getPlanificationPeriods(): Collection
    {
        return $this->planificationPeriods;
    }

    public function addPlanificationPeriod(PlanificationPeriod $planificationPeriod): self
    {
        if (!$this->planificationPeriods->contains($planificationPeriod)) {
            $this->planificationPeriods[] = $planificationPeriod;
            $planificationPeriod->setPlanification($this);
        }

        return $this;
    }

    public function removePlanificationPeriod(PlanificationPeriod $planificationPeriod): self
    {
        if ($this->planificationPeriods->contains($planificationPeriod)) {
            $this->planificationPeriods->removeElement($planificationPeriod);
            // set the owning side to null (unless already changed)
            if ($planificationPeriod->getPlanification() === $this) {
                $planificationPeriod->setPlanification(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Booking[]
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): self
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings[] = $booking;
            $booking->setPlanification($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->contains($booking)) {
            $this->bookings->removeElement($booking);
            // set the owning side to null (unless already changed)
            if ($booking->getPlanification() === $this) {
                $booking->setPlanification(null);
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
            $bookingLine->setPlanification($this);
        }

        return $this;
    }

    public function removeBookingLine(BookingLine $bookingLine): self
    {
        if ($this->bookingLines->contains($bookingLine)) {
            $this->bookingLines->removeElement($bookingLine);
            // set the owning side to null (unless already changed)
            if ($bookingLine->getPlanification() === $this) {
                $bookingLine->setPlanification(null);
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
