<?php
namespace App\Event;

use App\Entity\File;
use App\Entity\UserFileGroup;
use App\Entity\PlanificationPeriod;
use App\Entity\PlanificationLine;
use App\Entity\PlanificationResource;
use App\Entity\PlanificationViewUserFileGroup;

class PlanificationPeriodEvent
{
    static function postPersist($em, \App\Entity\User $user, \App\Entity\PlanificationPeriod $planificationPeriod)
    {
      PlanificationPeriodEvent::createPlanificationLines($em, $user, $planificationPeriod);
      PlanificationPeriodEvent::createPlanificationresources($em, $user, $planificationPeriod);
      PlanificationPeriodEvent::createPlanificationViewUserFileGroups($em, $user, $planificationPeriod);
    }

    // Crée les lignes de la période de planification
    static function createPlanificationLines($em, \App\Entity\User $user, \App\Entity\PlanificationPeriod $planificationPeriod)
    {
      $ppRepository = $em->getRepository(PlanificationPeriod::class);
      $plRepository = $em->getRepository(PlanificationLine::class);
      $previousPP = $ppRepository->getPreviousPlanificationPeriod($planificationPeriod->getPlanification(), $planificationPeriod->getID());

      if ($previousPP !== null) { // Nouvelle période de planification.
        // Recherche des lignes de planification de la période précédente
        $previousPLs = $plRepository->getLines($previousPP);

        // Recopie des lignes de planification de la période précédente vers la période en cours
        foreach ($previousPLs as $previousPL) {
            $planificationLine = new PlanificationLine($user, $planificationPeriod, $previousPL->getWeekDay(), $previousPL->getOrder());
            $planificationLine->setActive($previousPL->getActive());
            if ($planificationLine->getActive()) {
                $planificationLine->setTimetable($previousPL->getTimetable());
            }
            $em->persist($planificationLine);
        }
        $em->flush();
    }
  }

  // Crée les ressources de la période de planification
  static function createPlanificationresources($em, \App\Entity\User $user, \App\Entity\PlanificationPeriod $planificationPeriod)
  {
    $ppRepository = $em->getRepository(PlanificationPeriod::class);
    $prRepository = $em->getRepository(PlanificationResource::class);
    $previousPP = $ppRepository->getPreviousPlanificationPeriod($planificationPeriod->getPlanification(), $planificationPeriod->getID());

    if ($previousPP !== null) { // Nouvelle période de planification.
      // Recherche des ressources de la période précédente
      $previousPRs = $prRepository->getResources($previousPP);

      // Recopie des ressources planifiées de la période précédente vers la période en cours
      foreach ($previousPRs as $previousPR) {
          $planificationResource = new PlanificationResource($user, $planificationPeriod, $previousPR->getResource());
          $planificationResource->setOrder($previousPR->getOrder());
          $em->persist($planificationResource);
      }
      $em->flush();
    }
  }
    // Crée les vues de la période de planification
    static function createPlanificationViewUserFileGroups($em, \App\Entity\User $user, \App\Entity\PlanificationPeriod $planificationPeriod)
    {
	$ppRepository = $em->getRepository(PlanificationPeriod::class);
	$pvufgRepository = $em->getRepository(PlanificationViewUserFileGroup::class);
  $ufgRepository = $em->getRepository(UserFileGroup::class);

	$previousPP = $ppRepository->getPreviousPlanificationPeriod($planificationPeriod->getPlanification(), $planificationPeriod->getID());
	if ($previousPP === null) { // Première période de la planification. On crée une vue pour le groupe de tous les utilisateurs.

		$allUserGroup = $ufgRepository->findOneBy(array('file' => $planificationPeriod->getPlanification()->getFile(), 'type' => 'ALL')); // Recherche du groupe de tous les utilisateurs.
		$planificationViewUserFileGroup = new PlanificationViewUserFileGroup($user, $planificationPeriod, $allUserGroup);
		$em->persist($planificationViewUserFileGroup);

	} else { // Période suivante: on recopie les vues de la période précédente
		$previousPVs = $pvufgRepository->getViews($previousPP);
		foreach ($previousPVs as $previousPV) {
			$planificationViewUserFileGroup = new PlanificationViewUserFileGroup($user, $planificationPeriod, $previousPV->getUserFileGroup());
			$planificationViewUserFileGroup->setActive($previousPV->getActive());
			$planificationViewUserFileGroup->setOrder($previousPV->getOrder());
      $em->persist($planificationViewUserFileGroup);
		}
	}
    $em->flush();
    }
}
