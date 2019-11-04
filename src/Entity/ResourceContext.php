<?php
namespace App\Entity;

class ResourceContext
{
    protected $planificationsCount;

    public function setPlanificationsCount($planificationsCount)
    {
    $this->planificationsCount = $planificationsCount;
    return $this;
    }

    public function getPlanificationsCount()
    {
    return $this->planificationsCount;
    }

    function __construct($em, \App\Entity\File $file, \App\Entity\Resource $resource)
    {
    $pRepository = $em->getRepository(Planification::Class);
    $this->setPlanificationsCount($pRepository->getResourcePlanificationsCount($file, $resource));
    return $this;
    }
}
