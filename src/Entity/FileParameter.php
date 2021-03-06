<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="uk_file_parameter",columns={"file_id", "parameter_group", "parameter"})})
 * @ORM\Entity(repositoryClass="App\Repository\FileParameterRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class FileParameter
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
    private $parameterGroup;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $parameter;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $parameterType;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $integerValue;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $stringValue;

    /**
     * @ORM\Column(type="boolean")
     */
    private $booleanValue;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\File", inversedBy="fileParameters")
     * @ORM\JoinColumn(nullable=false)
     */
    private $file;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="fileParameters")
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

    public function getParameterGroup(): ?string
    {
        return $this->parameterGroup;
    }

    public function setParameterGroup(string $parameterGroup): self
    {
        $this->parameterGroup = $parameterGroup;
        return $this;
    }

    public function getParameter(): ?string
    {
        return $this->parameter;
    }

    public function setParameter(string $parameter): self
    {
        $this->parameter = $parameter;
        return $this;
    }

    public function getParameterType(): ?string
    {
        return $this->parameterType;
    }

    public function setParameterType(string $parameterType): self
    {
        $this->parameterType = $parameterType;
        return $this;
    }

    public function getIntegerValue(): ?int
    {
        return $this->integerValue;
    }

    public function setIntegerValue(?int $integerValue): self
    {
        $this->integerValue = $integerValue;
        return $this;
    }

    public function getStringValue(): ?string
    {
        return $this->stringValue;
    }

    public function setStringValue(?string $stringValue): self
    {
        $this->stringValue = $stringValue;
        return $this;
    }

    public function getBooleanValue(): ?bool
    {
        return $this->booleanValue;
    }

    public function setBooleanValue(?bool $booleanValue): self
    {
        $this->booleanValue = $booleanValue;
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function __construct(?User $user, ?File $file, $parameterGroup, $parameter) {
  		$this->setUser($user);
  		$this->setFile($file);
  		$this->setParameterGroup($parameterGroup);
  		$this->setParameter($parameter);
  		$this->setBooleanValue(false);
      }

    public function setSBIntegerValue(?int $integerValue): self
  	{
		$this->setIntegerValue($integerValue);
    $this->setParameterType('integer');
		return $this;
    }

    public function setSBStringValue(?string $stringValue): self
    {
		$this->setStringValue($stringValue);
		$this->setParameterType('string');
		return $this;
    }

    public function setSBBooleanValue(bool $booleanValue): self
    {
		$this->setBooleanValue($booleanValue);
		$this->setParameterType('boolean');
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
