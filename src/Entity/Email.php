<?php
namespace App\Entity;

class Email
{
	protected $email;

	public function setEmail($email)
	{
		$this->email = $email;
		return $this;
	}

	public function getEmail()
	{
		return $this->email;
	}
    
	function __construct() {
		$this->setEmail("");
	}
}
