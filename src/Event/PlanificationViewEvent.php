<?php
namespace App\Event;

use App\Entity\PlanificationPeriod;
use App\Entity\PlanificationResource;
use App\Entity\PlanificationView;
use App\Entity\PlanificationViewResource;

class PlanificationViewEvent
{
    static function postPersist($em, \App\Entity\User $user, \App\Entity\PlanificationView $planificationView)
    {
      PlanificationViewEvent::createPlanificationViewResources($em, $user, $planificationView);
    }

	// Rattache les ressources planifiées à la vue créée
    static function createPlanificationViewResources($em, \App\Entity\User $user, \App\Entity\PlanificationView $planificationView)
    {
	$ppRepository = $em->getRepository(PlanificationPeriod::class);
	$prRepository = $em->getRepository(PlanificationResource::class);
	$pvrRepository = $em->getRepository(PlanificationViewResource::class);

	// Recherche des ressources planifiées de la période de la vue créée
	$planificationResources = $prRepository->findBy(array('planificationPeriod' => $planificationView->getPlanificationPeriod()), array('oorder' => 'asc'));

	// Recherche de la période précédente de la prériode à laquelle est rattachée la vue
	$previousPP = $ppRepository->getPreviousPlanificationPeriod($planificationView->getPlanificationPeriod()->getPlanification(), $planificationView->getPlanificationPeriod()->getID());

	// Chaque ressource planifiée est rattachée à la vue créée
	foreach ($planificationResources as $planificationResource) {
		$planificationViewResource = new PlanificationViewResource($planificationView, $planificationResource);

		if ($previousPP === null) { // Première période de la planification. On active toutes les ressources.
			$active = true;
		} else {
			$previousPVR = $pvrRepository->findOneBy(array('planificationView' => $previousPP, 'planificationResource' => $planificationResource)); // Recherche de la vue-ressource de la période précédente.
			if ($previousPVR === null) { // Si la vue ressource de la période précédente n'est pas trouvée, on active la vue-ressource de la période créée, mais logiquement les vues-ressources existent pour chaque période.
				$active = true;
			} else {
				$active = $previousPVR->getActive();
			}
		}
		$planificationViewResource->setActive($active);
		$em->persist($planificationViewResource);
	}
    $em->flush();
    }
}
