<?php
namespace App\Entity;

class UserParameterNLC
{
	protected $numberLines = 0;
	protected $numberColumns = 0;

	public function setNumberLines($numberLines)
	{
		$this->numberLines = $numberLines;
		return $this;
	}

	public function getNumberLines()
	{
		return $this->numberLines;
	}

	public function setNumberColumns($numberColumns)
	{
		$this->numberColumns = $numberColumns;
		return $this;
	}

	public function getNumberColumns()
	{
		return $this->numberColumns;
	}

	function __construct($numberLines, $numberColumns) {
		$this->setNumberLines($numberLines);
		$this->setNumberColumns($numberColumns);
	}
}
