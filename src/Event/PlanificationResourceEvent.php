<?php
namespace App\Event;

use App\Entity\PlanificationPeriod;
use App\Entity\PlanificationResource;
use App\Entity\PlanificationView;
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
	$pvRepository = $em->getRepository(PlanificationView::class);

    // Recherche des vues de la période de planification
	$planificationViews = $pvRepository->findBy(array('planificationPeriod' => $planificationResource->getPlanificationPeriod()), array('oorder' => 'asc'));

    // A chaque vue, on attache et active la ressource créée
	foreach ($planificationViews as $planificationView) {
		$planificationViewResource = new PlanificationViewResource($planificationView, $planificationResource);
		$planificationViewResource->setActive(true);
		$em->persist($planificationViewResource);
	}
    $em->flush();
    }
}
