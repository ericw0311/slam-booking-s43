<?php
namespace App\Entity;

class Ddate
{
	protected $date;

	public function setDate($date)
	{
		$this->date = $date;
		return $this;
	}

	public function getDate()
	{
		return $this->date;
	}
}
