<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="uk_booking_duplication",columns={"origin_booking_id", "ddate"})})
 * @ORM\Entity(repositoryClass="App\Repository\BookingDuplicationRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class BookingDuplication
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $gap;

    /**
     * @ORM\Column(type="date")
     */
    private $ddate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Booking", inversedBy="bookingDuplications")
     * @ORM\JoinColumn(nullable=false)
     */
    private $originBooking;

    // J'ai supprimé le cascade "delete" pour éviter que la suppression d'une réservation duppliquée ne supprime la réservation d'origine.
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Booking", inversedBy="newBookingDuplication", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $newBooking;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="bookingDuplications")
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

    public function getGap(): ?int
    {
        return $this->gap;
    }

    public function setGap(int $gap): self
    {
        $this->gap = $gap;
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

    public function getOriginBooking(): ?Booking
    {
        return $this->originBooking;
    }

    public function setOriginBooking(?Booking $originBooking): self
    {
        $this->originBooking = $originBooking;
        return $this;
    }

    public function getNewBooking(): ?Booking
    {
        return $this->newBooking;
    }

    public function setNewBooking(Booking $newBooking): self
    {
        $this->newBooking = $newBooking;
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

    public function __construct(?User $user, ?Booking $originBooking, \DateTimeInterface $ddate, $gap, ?Booking $newBooking)
  	{
  	$this->setUser($user);
  	$this->setOriginBooking($originBooking);
  	$this->setDate($ddate);
  	$this->setGap($gap);
  	$this->setNewBooking($newBooking);
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
