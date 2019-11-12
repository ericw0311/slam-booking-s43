<?php
namespace App\Event;

use App\Entity\File;
use App\Entity\UserFileGroup;
use App\Entity\PlanificationPeriod;
use App\Entity\PlanificationView;

class PlanificationPeriodEvent
{
    static function postPersist($em, \App\Entity\User $user, \App\Entity\PlanificationPeriod $planificationPeriod)
    {
      PlanificationPeriodEvent::createPlanificationView($em, $user, $planificationPeriod);
    }

    // Rattache l'utilisateur courant au dossier
    static function createPlanificationView($em, \App\Entity\User $user, \App\Entity\PlanificationPeriod $planificationPeriod)
    {
	$ufgRepository = $em->getRepository(UserFileGroup::class);
	$ppRepository = $em->getRepository(PlanificationPeriod::class);
	$pvRepository = $em->getRepository(PlanificationView::class);

	$previousPP = $ppRepository->getPreviousPlanificationPeriod($planificationPeriod->getPlanification(), $planificationPeriod->getID());
	if ($previousPP === null) { // Première période de la planification. On crée une vue pour le groupe de tous les utilisateurs.

		$allUserGroup = $ufgRepository->findOneBy(array('file' => $planificationPeriod->getPlanification()->getFile(), 'type' => 'ALL')); // Recherche du groupe de tous les utilisateurs.
		$planificationView = new PlanificationView($user, $planificationPeriod, $allUserGroup);
		$em->persist($planificationView);

	} else { // Période suivante: on recopie les vues de la période précédente
		$previousPlanificationViews = $pvRepository->getViews($previousPP);
		foreach ($previousPlanificationViews as $previousPlanificationView) {
			$planificationView = new PlanificationView($user, $planificationPeriod, $previousPlanificationView->getUserFileGroup());
			$planificationView->setActive($previousPlanificationView->getActive());
			$planificationView->setOrder($previousPlanificationView->getOrder());
      $em->persist($planificationView);
		}
	}
    $em->flush();
    }
}
