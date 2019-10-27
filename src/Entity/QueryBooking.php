<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
* @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="uk_query_booking",columns={"file_id", "name"})})
* @ORM\Entity(repositoryClass="App\Repository\QueryBookingRepository")
* @ORM\HasLifecycleCallbacks()
* @UniqueEntity(fields={"file", "name"}, errorPath="name", message="dashboard.already.exists")
*/
class QueryBooking
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
     */
    private $periodType = "NO";

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $beginningDate;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $endDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $userType = "ALL";

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $resourceType = "ALL";

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="queryBookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\File", inversedBy="queryBookings")
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

    public function getPeriodType(): ?string
    {
        return $this->periodType;
    }

    public function setPeriodType(string $periodType): self
    {
        $this->periodType = $periodType;

        return $this;
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

    public function getUserType(): ?string
    {
        return $this->userType;
    }

    public function setUserType(string $userType): self
    {
        $this->userType = $userType;

        return $this;
    }

    public function getResourceType(): ?string
    {
        return $this->resourceType;
    }

    public function setResourceType(string $resourceType): self
    {
        $this->resourceType = $resourceType;

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

    public function __construct(?User $user, ?File $file)
      {
      $this->setUser($user);
      $this->setFile($file);
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
