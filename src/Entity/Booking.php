<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookingRepository")
 */
class Booking
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Planification", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $planification;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Resource", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $resource;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $note;

    /**
     * @ORM\Column(type="datetime")
     */
    private $beginningDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $endDate;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Note", inversedBy="booking", cascade={"persist", "remove"})
     */
    private $formNote;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\File", inversedBy="bookings")
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
     * @ORM\OneToMany(targetEntity="App\Entity\BookingLine", mappedBy="booking", orphanRemoval=true)
     */
    private $bookingLines;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BookingLabel", mappedBy="booking", orphanRemoval=true)
     */
    private $bookingLabels;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BookingUser", mappedBy="booking", orphanRemoval=true)
     */
    private $bookingUsers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BookingDuplication", mappedBy="originBooking", orphanRemoval=true)
     */
    private $bookingDuplications;

    public function __construct(?User $user, ?File $file, ?Planification $planification, ?Resource $resource)
    {
      $this->setUser($user);
      $this->setFile($file);
      $this->setPlanification($planification);
      $this->setResource($resource);
      $this->bookingLines = new ArrayCollection();
      $this->bookingLabels = new ArrayCollection();
      $this->bookingUsers = new ArrayCollection();
      $this->bookingDuplications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getResource(): ?Resource
    {
        return $this->resource;
    }

    public function setResource(?Resource $resource): self
    {
        $this->resource = $resource;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function setNullFormNote()
  	{
  		$this->formNote = null;
  		return $this;
      }

    public function getBeginningDate(): ?\DateTimeInterface
    {
        return $this->beginningDate;
    }

    public function setBeginningDate(\DateTimeInterface $beginningDate): self
    {
        $this->beginningDate = $beginningDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getFormNote(): ?Note
    {
        return $this->formNote;
    }

    public function setFormNote(?Note $formNote): self
    {
        $this->formNote = $formNote;

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
            $bookingLine->setBooking($this);
        }

        return $this;
    }

    public function removeBookingLine(BookingLine $bookingLine): self
    {
        if ($this->bookingLines->contains($bookingLine)) {
            $this->bookingLines->removeElement($bookingLine);
            // set the owning side to null (unless already changed)
            if ($bookingLine->getBooking() === $this) {
                $bookingLine->setBooking(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|BookingLabel[]
     */
    public function getBookingLabels(): Collection
    {
        return $this->bookingLabels;
    }

    public function addBookingLabel(BookingLabel $bookingLabel): self
    {
        if (!$this->bookingLabels->contains($bookingLabel)) {
            $this->bookingLabels[] = $bookingLabel;
            $bookingLabel->setBooking($this);
        }

        return $this;
    }

    public function removeBookingLabel(BookingLabel $bookingLabel): self
    {
        if ($this->bookingLabels->contains($bookingLabel)) {
            $this->bookingLabels->removeElement($bookingLabel);
            // set the owning side to null (unless already changed)
            if ($bookingLabel->getBooking() === $this) {
                $bookingLabel->setBooking(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|BookingUser[]
     */
    public function getBookingUsers(): Collection
    {
        return $this->bookingUsers;
    }

    public function addBookingUser(BookingUser $bookingUser): self
    {
        if (!$this->bookingUsers->contains($bookingUser)) {
            $this->bookingUsers[] = $bookingUser;
            $bookingUser->setBooking($this);
        }

        return $this;
    }

    public function removeBookingUser(BookingUser $bookingUser): self
    {
        if ($this->bookingUsers->contains($bookingUser)) {
            $this->bookingUsers->removeElement($bookingUser);
            // set the owning side to null (unless already changed)
            if ($bookingUser->getBooking() === $this) {
                $bookingUser->setBooking(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|BookingDuplication[]
     */
    public function getBookingDuplications(): Collection
    {
        return $this->bookingDuplications;
    }

    public function addBookingDuplication(BookingDuplication $bookingDuplication): self
    {
        if (!$this->bookingDuplications->contains($bookingDuplication)) {
            $this->bookingDuplications[] = $bookingDuplication;
            $bookingDuplication->setOriginBooking($this);
        }

        return $this;
    }

    public function removeBookingDuplication(BookingDuplication $bookingDuplication): self
    {
        if ($this->bookingDuplications->contains($bookingDuplication)) {
            $this->bookingDuplications->removeElement($bookingDuplication);
            // set the owning side to null (unless already changed)
            if ($bookingDuplication->getOriginBooking() === $this) {
                $bookingDuplication->setOriginBooking(null);
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
