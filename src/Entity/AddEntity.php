<?php
namespace App\Entity;

class AddEntity
{
    private $id = 0;
    private $name;
    private $imageName;
    private $imageType; // M = iMage, C = iCone
    private $entityIDList_select;

    public function setId($id)
    {
		$this->id = $id;
        return $this;
    }
    
    public function getId()
    {
        return $this->id;
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
    
    public function setImageName($imageName)
    {
        $this->imageType = 'M';
        $this->imageName = $imageName;
        return $this;
    }
    
    public function setIconName($iconName)
    {
        $this->imageType = 'C';
        $this->imageName = $iconName;
        return $this;
    }
    
    public function getImageName()
    {
        return $this->imageName;
    }
    
    public function getImageType()
    {
        return $this->imageType;
    }
    
    public function setEntityIDList_select($entityIDList)
    {
        $this->entityIDList_select = $entityIDList;
        return $this;
    }
    
    public function getEntityIDList_select()
    {
        return $this->entityIDList_select;
    }
}
