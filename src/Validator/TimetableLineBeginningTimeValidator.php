<?php
// App/Validator/TimetableLineBeginningTimeValidator.php

namespace App\Validator;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use App\Entity\Timetable;
use App\Entity\TimetableLine;

class TimetableLineBeginningTimeValidator extends ConstraintValidator
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
    $this->em = $em;
    }

    public function getEntityManager()
    {
    return $this->em;
    }

    public function validate($timetableLine, Constraint $constraint)
    {
    $em = $this->getEntityManager();
    $tRepository = $em->getRepository(Timetable::Class);
    $timetable = $tRepository->find($timetableLine->getTimetable()->getID());
	$tlRepository = $em->getRepository(TimetableLine::Class);
	$previousTimetableLine = null;

	if ($timetableLine->getId() > 0) { // On est en mise a jour de créneau horaire
		$previousTimetableLine = $tlRepository->getPreviousTimetableLine($timetable, $timetableLine->getId());
	} else { // On est en création de créneau horaire
		$previousTimetableLine = $tlRepository->getLastTimetableLine($timetable);
	}

	if ($previousTimetableLine != null) { // Il existe un créneau précédent
		$interval = date_diff($previousTimetableLine->getEndTime(), $timetableLine->getBeginningTime());
		if ($interval->format("%R") == "-") { // L'heure de début est inférieure à l'heure de fin du créneau précédent
			$this->context->buildViolation($constraint->message)
				->setParameter('%endTime%', date_format($previousTimetableLine->getEndTime(), "H:i"))
				->addViolation();
		}
	}
    }
}
