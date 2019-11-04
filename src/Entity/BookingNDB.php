<?php
namespace App\Entity;

// NDB = not database
class BookingNDB
{
	private $id;
	private $type;
	private $numberTimetableLines;
	private $cellClass;
	private $note;
	private $userId;
	private $numberUsers;
	private $firstUserName;
	private $numberLabels;
	private $firstLabelName;

	public function setId($id)
	{
	$this->id = $id;
	return $this;
	}

	public function getId()
	{
	return $this->id;
	}

	public function setType($type)
	{
	$this->type = $type;
	return $this;
	}

	public function getType()
	{
	return $this->type;
	}

	public function setNumberTimetableLines($numberTimetableLines)
	{
	$this->numberTimetableLines = $numberTimetableLines;
	return $this;
	}

	public function getNumberTimetableLines()
	{
	return $this->numberTimetableLines;
	}

	public function setCellClass($cellClass)
	{
	$this->cellClass = $cellClass;
	return $this;
	}

	public function getCellClass()
	{
	return $this->cellClass;
	}

	public function setFirstUserName($firstUserName)
	{
	$this->firstUserName = $firstUserName;
	return $this;
	}

	public function getFirstUserName()
	{
	return $this->firstUserName;
	}
	
	public function setNumberUsers($numberUsers)
	{
	$this->numberUsers = $numberUsers;
	return $this;
	}

	public function getNumberUsers()
	{
	return $this->numberUsers;
	}

	public function setFirstLabelName($firstLabelName)
	{
	$this->firstLabelName = $firstLabelName;
	return $this;
	}

	public function getFirstLabelName()
	{
	return $this->firstLabelName;
	}
	
	public function setNumberLabels($numberLabels)
	{
	$this->numberLabels = $numberLabels;
	return $this;
	}

	public function getNumberLabels()
	{
	return $this->numberLabels;
	}

	public function setNote($note)
	{
	$this->note = $note;
	return $this;
	}

	public function getNote()
	{
	return $this->note;
	}
	
	public function setUserId($userId)
	{
	$this->userId = $userId;
	return $this;
	}

	public function getUserId()
	{
	return $this->userId;
	}

	public function __construct($id, $type, $cellClass)
	{
	$this->setId($id);
	$this->setType($type);
	$this->setNumberTimetableLines(0);
	$this->setCellClass($cellClass);
	$this->setFirstUserName(null);
	$this->setNumberUsers(0);
	$this->setFirstLabelName(null);
	$this->setNumberLabels(0);
	$this->setNote(null);
	$this->setUserId(0);
	}

	public function getNoteExists()
	{
	return (!empty($this->note));
	}
}
