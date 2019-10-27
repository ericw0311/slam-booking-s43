<?php
// App/Validator/TimetableLineBeginningTime.php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class TimetableLineBeginningTime extends Constraint
{
    public $message = 'timetableLine.order.beginningTime.control';
    
    public function getTargets()
    {
    return self::CLASS_CONSTRAINT;
    }
    
    public function validatedBy()
    {
    return 'timetableLineBeginningTime';
    }
}
