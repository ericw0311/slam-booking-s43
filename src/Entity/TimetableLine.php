<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

use App\Validator\TimetableLineBeginningTime;
use App\Validator\TimetableLineEndTime;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TimetableLineRepository")
 * @ORM\HasLifecycleCallbacks()
 * @TimetableLineBeginningTime()
 * @TimetableLineEndTime()
 */
class TimetableLine
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Choice({"T", "D", "AM", "PM"})
     */
    private $type;

    /**
     * @ORM\Column(type="time")
     */
    private $beginningTime;

    /**
     * @ORM\Column(type="time")
     */
    private $endTime;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Timetable", inversedBy="timetableLines")
     * @ORM\JoinColumn(nullable=false)
     */
    private $timetable;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="timetableLines")
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

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BookingLine", mappedBy="timetableLine")
     */
    private $bookingLines;


    public function __construct(?User $user, ?Timetable $timetable)
      {
      $this->setUser($user);
      $this->setTimetable($timetable);
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

    public function getBeginningTime(): ?\DateTimeInterface
    {
        return $this->beginningTime;
    }

    public function setBeginningTime(\DateTimeInterface $beginningTime): self
    {
        $this->beginningTime = $beginningTime;
        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeInterface $endTime): self
    {
        $this->endTime = $endTime;
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

    /**
      * @Assert\IsTrue(message="timetableLine.endTime.control")
      */
      public function isEndTime()
      {
  	$interval = date_diff($this->getEndTime(), $this->getBeginningTime());
  	return ($interval->format("%R") == "-");
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
            $bookingLine->setTimetableLine($this);
        }

        return $this;
    }

    public function removeBookingLine(BookingLine $bookingLine): self
    {
        if ($this->bookingLines->contains($bookingLine)) {
            $this->bookingLines->removeElement($bookingLine);
            // set the owning side to null (unless already changed)
            if ($bookingLine->getTimetableLine() === $this) {
                $bookingLine->setTimetableLine(null);
            }
        }

        return $this;
    }
}
