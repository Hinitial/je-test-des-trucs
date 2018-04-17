<?php
/**
 * Created by PhpStorm.
 * User: romain
 * Date: 10/04/18
 * Time: 11:52
 */

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NoPastDateValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $dateActuel = new \DateTime();

        if($dateActuel > $value && !($dateActuel->format('Y-m-d') == $value->format('Y-m-d'))){
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ jourVisite }}', $value->format('Y-m-d'))
                ->addViolation();
        }
    }
}