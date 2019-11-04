<?php
namespace App\Entity;

class SelectedEntity
{
    private $id = 0;
    private $name;
    private $imageName;
    private $imageType; // M = iMage, C = iCone
    private $entityIDList_sortAfter;
    private $entityIDList_sortBefore;
    private $entityIDList_unselect;
    
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
    
    public function setEntityIDList_sortAfter($entityIDList)
    {
        $this->entityIDList_sortAfter = $entityIDList;
        return $this;
    }
    
    public function getEntityIDList_sortAfter()
    {
        return $this->entityIDList_sortAfter;
    }
    
    public function setEntityIDList_sortBefore($entityIDList)
    {
        $this->entityIDList_sortBefore = $entityIDList;
        return $this;
    }
    
    public function getEntityIDList_sortBefore()
    {
        return $this->entityIDList_sortBefore;
    }
    
    public function setEntityIDList_unselect($entityIDList)
    {
        $this->entityIDList_unselect = $entityIDList;
        return $this;
    }
    
    public function getEntityIDList_unselect()
    {
        return $this->entityIDList_unselect;
    }
}
