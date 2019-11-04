<?php
namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class PlanificationPeriodCreateDate
{
	protected $bookingMaxDate;
	protected $date;

	public function setDate(\Datetime $date)
	{
		$this->date = $date;
		return $this;
	}

	public function getDate()
	{
		return $this->date;
	}

	public function setBookingMaxDate(\Datetime $maxDate)
	{
		$this->bookingMaxDate = $maxDate;
		return $this;
	}

	public function getBookingMaxDate()
	{
		return $this->bookingMaxDate;
	}


    function __construct(\Datetime $maxDate)
    {
	$this->setBookingMaxDate($maxDate);
    return $this;
    }

	/**
    * @Assert\IsTrue(message="planificationPeriod.date.control")
    */
    public function isDate()
    {
	$interval = date_diff($this->getDate(), $this->getBookingMaxDate());
	return ($interval->format("%R") == "-");
    }
}
