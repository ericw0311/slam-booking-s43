<?php
namespace App\Event;

use App\Entity\PlanificationPeriod;
use App\Entity\PlanificationResource;
use App\Entity\PlanificationViewUserFileGroup;
use App\Entity\PlanificationViewResource;

class PlanificationResourceEvent
{
    static function postPersist($em, \App\Entity\User $user, \App\Entity\PlanificationResource $planificationResource)
    {
      PlanificationResourceEvent::createPlanificationViewResources($em, $user, $planificationResource);
    }

    // Rattache la ressource planifiée à toutes les vues de la période de planification
    static function createPlanificationViewResources($em, \App\Entity\User $user, \App\Entity\PlanificationResource $planificationResource)
    {
	$pvufgRepository = $em->getRepository(PlanificationViewUserFileGroup::class);

    // Recherche des vues de la période de planification
	$planificationViewUserFileGroups = $pvufgRepository->findBy(array('planificationPeriod' => $planificationResource->getPlanificationPeriod()), array('oorder' => 'asc'));

    // A chaque vue, on attache et active la ressource créée
	foreach ($planificationViewUserFileGroups as $planificationViewUserFileGroup) {
		$planificationViewResource = new PlanificationViewResource($planificationViewUserFileGroup, $planificationResource);
		$planificationViewResource->setActive(true);
		$em->persist($planificationViewResource);
	}
    $em->flush();
    }
}
