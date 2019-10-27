<?php
// App/Validator/TimetableLineEndTime.php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class TimetableLineEndTime extends Constraint
{
    public $message = 'timetableLine.order.endTime.control';
    
    public function getTargets()
    {
    return self::CLASS_CONSTRAINT;
    }
    
    public function validatedBy()
    {
    return 'timetableLineEndTime';
    }
}
