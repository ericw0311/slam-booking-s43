<?php
namespace App\Entity;

// Classe utilisée pour l'affichage de la liste de ressources
class Resource_List
{
	
	private $id;
	private $internal;
	private $type;
	private $code;
	private $name;
	private $planified;

    public function setId($id)
    {
	$this->id = $id;
	return $this;
    }
    
    public function getId()
    {
	return $this->id;
    }
    
    public function setInternal($internal)
    {
	$this->internal = $internal;
	return $this;
    }
    
    public function getInternal()
    {
	return $this->internal;
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
    
    public function setCode($code)
    {
	$this->code = $code;
	return $this;
    }
    
    public function getCode()
    {
	return $this->code;
    }
    
    public function setName($name)
    {
	$this->name = $name;
	return $this;
    }
    
    public function getName()
    {
	return $this->name;
    }

    public function setPlanified($planified)
    {
	$this->planified = $planified;
	return $this;
    }
    
    public function getPlanified()
    {
	return $this->planified;
    }

    public function __construct($id, $internal, $type, $code, $name)
    {
	$this->setId($id);
	$this->setInternal($internal);
	$this->setType($type);
	$this->setCode($code);
	$this->setName($name);
	$this->setPlanified(false);
    }
}
