<?php
/**
 * Created by PhpStorm.
 * User: romain
 * Date: 10/04/18
 * Time: 12:21
 */

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NoFutureDateValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $dateActuel = new \DateTime();

        if($dateActuel < $value){
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ jourVisite }}', $value->format('Y-m-d'))
                ->addViolation();
        }
    }
}