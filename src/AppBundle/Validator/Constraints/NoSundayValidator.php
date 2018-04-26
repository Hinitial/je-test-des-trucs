<?php
/**
 * Created by PhpStorm.
 * User: romain
 * Date: 10/04/18
 * Time: 12:37
 */

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NoSundayValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $jour = $value->format('N');

        if($jour == '7'){
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}