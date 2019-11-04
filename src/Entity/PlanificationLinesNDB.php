<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

class PlanificationLinesNDB
{
    private $planificationPeriod;
    private $activate_MON;
    private $activate_TUE;
    private $activate_WED;
    private $activate_THU;
    private $activate_FRI;
    private $activate_SAT;
    private $activate_SUN;
    private $timetable_MON;
    private $timetable_TUE;
    private $timetable_WED;
    private $timetable_THU;
    private $timetable_FRI;
    private $timetable_SAT;
    private $timetable_SUN;

    public function setPlanificationPeriod(\App\Entity\PlanificationPeriod $planificationPeriod)
    {
        $this->planificationPeriod = $planificationPeriod;
        return $this;
    }

    public function getPlanificationPeriod()
    {
        return $this->planificationPeriod;
    }

    public function setTimetableMON(\App\Entity\Timetable $timetable)
    {
        $this->timetable_MON = $timetable;
        return $this;
    }

    public function getTimetableMON()
    {
        return $this->timetable_MON;
    }

    public function setTimetableTUE(\App\Entity\Timetable $timetable)
    {
        $this->timetable_TUE = $timetable;
        return $this;
    }

    public function getTimetableTUE()
    {
        return $this->timetable_TUE;
    }

    public function setTimetableWED(\App\Entity\Timetable $timetable)
    {
        $this->timetable_WED = $timetable;
        return $this;
    }

    public function getTimetableWED()
    {
        return $this->timetable_WED;
    }

    public function setTimetableTHU(\App\Entity\Timetable $timetable)
    {
        $this->timetable_THU = $timetable;
        return $this;
    }

    public function getTimetableTHU()
    {
        return $this->timetable_THU;
    }

    public function setTimetableFRI(\App\Entity\Timetable $timetable)
    {
        $this->timetable_FRI = $timetable;
        return $this;
    }

    public function getTimetableFRI()
    {
        return $this->timetable_FRI;
    }

    public function setTimetableSAT(\App\Entity\Timetable $timetable)
    {
        $this->timetable_SAT = $timetable;
        return $this;
    }

    public function getTimetableSAT()
    {
        return $this->timetable_SAT;
    }

    public function setTimetableSUN(\App\Entity\Timetable $timetable)
    {
        $this->timetable_SUN = $timetable;
        return $this;
    }

    public function getTimetableSUN()
    {
        return $this->timetable_SUN;
    }

    public function setActivateMON($activate)
    {
        $this->activate_MON = $activate;
        return $this;
    }

    public function getActivateMON()
    {
        return $this->activate_MON;
    }

    public function setActivateTUE($activate)
    {
        $this->activate_TUE = $activate;
        return $this;
    }

    public function getActivateTUE()
    {
        return $this->activate_TUE;
    }

	public function setActivateWED($activate)
	{
		$this->activate_WED = $activate;
		return $this;
	}

	public function getActivateWED()
	{
	return $this->activate_WED;
	}

	public function setActivateTHU($activate)
	{
		$this->activate_THU = $activate;
		return $this;
	}

	public function getActivateTHU()
	{
		return $this->activate_THU;
	}

	public function setActivateFRI($activate)
	{
		$this->activate_FRI = $activate;
		return $this;
	}

	public function getActivateFRI()
	{
		return $this->activate_FRI;
	}

	public function setActivateSAT($activate)
	{
		$this->activate_SAT = $activate;
		return $this;
	}

	public function getActivateSAT()
	{
		return $this->activate_SAT;
	}

	public function setActivateSUN($activate)
	{
		$this->activate_SUN = $activate;
		return $this;
	}
	public function getActivateSUN()
	{
		return $this->activate_SUN;
	}

    public function __construct(\App\Entity\PlanificationPeriod $planificationPeriod)
    {
		$this->setPlanificationPeriod($planificationPeriod);
    }
}
