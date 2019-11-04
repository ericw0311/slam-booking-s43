<?php
namespace App\Entity;

class FileBookingPeriod
{
	private $before; // Appliquer une restriction avant la date du jour
	private $beforeType; // Type de restriction avant la date du jour: JOUR, SEMAINE, MOIS, ANNEE
	private $beforeNumber; // Nombre associé à la restriction avant la date du jour

	private $after; // Appliquer une restriction après la date du jour
	private $afterType; // Type de restriction après la date du jour: JOUR, SEMAINE, MOIS, ANNEE
	private $afterNumber; // Nombre associé à la restriction après la date du jour

	public function getBefore()
	{
	return $this->before;
	}

	public function setBefore($before): self
	{
	$this->before = $before;
	return $this;
	}

	public function getBeforeType()
	{
	return $this->beforeType;
	}

	public function setBeforeType($beforeType): self
	{
	$this->beforeType = $beforeType;
	return $this;
	}

	public function getBeforeNumber()
	{
	return $this->beforeNumber;
	}

	public function setBeforeNumber($beforeNumber): self
	{
	$this->beforeNumber = $beforeNumber;
	return $this;
	}

	public function getAfter()
	{
	return $this->after;
	}

	public function setAfter($after): self
	{
	$this->after = $after;
	return $this;
	}

	public function getAfterType()
	{
	return $this->afterType;
	}

	public function setAfterType($afterType): self
	{
	$this->afterType = $afterType;
	return $this;
	}

	public function getAfterNumber()
	{
	return $this->afterNumber;
	}

	public function setAfterNumber($afterNumber): self
	{
	$this->afterNumber = $afterNumber;
	return $this;
	}

	public function __construct($before, $beforeType, $beforeNumber, $after, $afterType, $afterNumber)
	{
	$this->setBefore($before);
	$this->setBeforeType($beforeType);
	$this->setBeforeNumber($beforeNumber);
	$this->setAfter($after);
	$this->setAfterType($afterType);
	$this->setAfterNumber($afterNumber);
	}
}
