<?php
/**
 * Created by PhpStorm.
 * User: romain
 * Date: 10/04/18
 * Time: 12:55
 */

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NoTuesdayValidator extends  ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $day = $value->format('N');

        if($day == '2'){
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}