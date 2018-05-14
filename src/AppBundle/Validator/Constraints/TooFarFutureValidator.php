<?php
/**
 * Created by PhpStorm.
 * User: romain
 * Date: 29/04/18
 * Time: 13:32
 */

namespace AppBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class TooFarFutureValidator extends ConstraintValidator
{
    const MAX_DAY = 180;

    public function validate($value, Constraint $constraint)
    {
        $nowDate = new \DateTime();
        $diff = $nowDate->diff($value);

        if($diff->format('%a') > self::MAX_DAY){
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ visitDate }}', $value->format('Y-m-d'))
                ->addViolation();
        }
    }
}