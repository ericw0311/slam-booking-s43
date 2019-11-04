<?php
namespace App\Entity;

class FileBookingEmail
{
	private $fileAdministrator;
	private $bookingUser;

	public function getFileAdministrator()
	{
	return $this->fileAdministrator;
	}

	public function setFileAdministrator($fileAdministrator): self
	{
	$this->fileAdministrator = $fileAdministrator;
	return $this;
	}

	public function getBookingUser()
	{
	return $this->bookingUser;
	}

	public function setBookingUser($bookingUser): self
	{
	$this->bookingUser = $bookingUser;
	return $this;
	}

	public function __construct($fileAdministrator, $bookingUser)
	{
	$this->setFileAdministrator($fileAdministrator);
	$this->setBookingUser($bookingUser);
	}
}
