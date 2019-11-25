<?php
namespace App\Event;

use App\Entity\PlanificationPeriod;
use App\Entity\PlanificationResource;
use App\Entity\PlanificationViewUserFileGroup;
use App\Entity\PlanificationViewResource;

class PlanificationViewUserFileGroupEvent
{
    static function postPersist($em, \App\Entity\User $user, \App\Entity\PlanificationViewUserFileGroup $planificationViewUserFileGroup)
    {
      PlanificationViewUserFileGroupEvent::createPlanificationViewResources($em, $user, $planificationViewUserFileGroup);
    }

	// Rattache les ressources planifiées à la vue créée
    static function createPlanificationViewResources($em, \App\Entity\User $user, \App\Entity\PlanificationViewUserFileGroup $planificationViewUserFileGroup)
    {
	$ppRepository = $em->getRepository(PlanificationPeriod::class);
	$prRepository = $em->getRepository(PlanificationResource::class);
  $pvufgRepository = $em->getRepository(PlanificationViewUserFileGroup::class);
	$pvrRepository = $em->getRepository(PlanificationViewResource::class);

	// Recherche des ressources planifiées de la période de la vue créée
	$planificationResources = $prRepository->findBy(array('planificationPeriod' => $planificationViewUserFileGroup->getPlanificationPeriod()), array('oorder' => 'asc'));

	// Recherche de la période précédente de la prériode à laquelle est rattachée la vue
	$previousPP = $ppRepository->getPreviousPlanificationPeriod($planificationViewUserFileGroup->getPlanificationPeriod()->getPlanification(), $planificationViewUserFileGroup->getPlanificationPeriod()->getID());

	// Chaque ressource planifiée est rattachée à la vue créée
	foreach ($planificationResources as $planificationResource) {
		$planificationViewResource = new PlanificationViewResource($planificationViewUserFileGroup, $planificationResource);

		if ($previousPP === null) { // Première période de la planification. On active toutes les ressources.
			$active = true;
		} else {
      // Recherche de la vue de la période précédente du même groupe d'utilisateurs
      $previousPV = $pvufgRepository->findOneBy(array('planificationPeriod' => $previousPP, 'userFileGroup' => $planificationViewUserFileGroup->getUserFileGroup()));
      if ($previousPV === null) { // L'utilisateur crée une vue pour un groupe d'utilisateurs qui n'existe pas pour la période précédente.
        $active = true;
      } else {
        // Recherche de la ressource planifiée de la période précédente.
        $previousPR = $prRepository->findOneBy(array('planificationPeriod' => $previousPP, 'resource' => $planificationResource->getResource()));
        if ($previousPR === null) { // La ressource peut ne pas exister pour la période précédente.
          $active = true;
        } else {
          // Recherche de la vue-ressource de la période précédente pour le meme groupe d'utilisateurs et la même ressource.
          $previousPVR = $pvrRepository->findOneBy(array('planificationViewUserFileGroup' => $previousPV, 'planificationResource' => $previousPR));
          $active = $previousPVR->getActive();
        }
      }
    }
		$planificationViewResource->setActive($active);
		$em->persist($planificationViewResource);
	}
  $em->flush();
  }
}
