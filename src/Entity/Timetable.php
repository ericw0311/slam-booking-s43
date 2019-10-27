<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
* @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="uk_timetable",columns={"file_id", "name"})})
* @ORM\Entity(repositoryClass="App\Repository\TimetableRepository")
* @ORM\HasLifecycleCallbacks()
* @UniqueEntity(fields={"file", "name"}, errorPath="name", message="timetable.already.exists")
*/
class Timetable
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
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Choice({"T", "D", "HD"})
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="timetables")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\File", inversedBy="timetables")
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
     * @ORM\OneToMany(targetEntity="App\Entity\TimetableLine", mappedBy="timetable", orphanRemoval=true)
     */
    private $timetableLines;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PlanificationLine", mappedBy="timetable")
     */
    private $planificationLines;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BookingLine", mappedBy="timetable")
     */
    private $bookingLines;

    public function __construct(?User $user, ?File $file)
      {
  		$this->setUser($user);
  		$this->setFile($file);
      $this->timetableLines = new ArrayCollection();
      $this->planificationLines = new ArrayCollection();
      $this->bookingLines = new ArrayCollection();
      }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

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
     * @return Collection|TimetableLine[]
     */
    public function getTimetableLines(): Collection
    {
        return $this->timetableLines;
    }

    public function addTimetableLine(TimetableLine $timetableLine): self
    {
        if (!$this->timetableLines->contains($timetableLine)) {
            $this->timetableLines[] = $timetableLine;
            $timetableLine->setTimetable($this);
        }

        return $this;
    }

    public function removeTimetableLine(TimetableLine $timetableLine): self
    {
        if ($this->timetableLines->contains($timetableLine)) {
            $this->timetableLines->removeElement($timetableLine);
            // set the owning side to null (unless already changed)
            if ($timetableLine->getTimetable() === $this) {
                $timetableLine->setTimetable(null);
            }
        }

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
            $planificationLine->setTimetable($this);
        }

        return $this;
    }

    public function removePlanificationLine(PlanificationLine $planificationLine): self
    {
        if ($this->planificationLines->contains($planificationLine)) {
            $this->planificationLines->removeElement($planificationLine);
            // set the owning side to null (unless already changed)
            if ($planificationLine->getTimetable() === $this) {
                $planificationLine->setTimetable(null);
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
            $bookingLine->setTimetable($this);
        }

        return $this;
    }

    public function removeBookingLine(BookingLine $bookingLine): self
    {
        if ($this->bookingLines->contains($bookingLine)) {
            $this->bookingLines->removeElement($bookingLine);
            // set the owning side to null (unless already changed)
            if ($bookingLine->getTimetable() === $this) {
                $bookingLine->setTimetable(null);
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
