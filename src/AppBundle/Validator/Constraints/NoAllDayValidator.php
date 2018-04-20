<?php
/**
 * Created by PhpStorm.
 * User: romain
 * Date: 17/04/18
 * Time: 10:43
 */

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NoAllDayValidator extends ConstraintValidator
{
    public function validate($reservation, Constraint $constraint)
    {
        $now = new \DateTime();
        $date_now = $now->format('d/m/Y');
        $hour_now = (int) $now->format('G');

        $date_reservation = $reservation->getJourVisite();
        $date_reservation = $date_reservation->format('d/m/Y');

        if(($reservation->getTypeBillet() == 'journee') && ($date_reservation == $date_now) && ($hour_now >= 14)){
            $this->context->buildViolation($constraint->message)
//                ->atPath('typeBillet')
                ->addViolation();
        }
    }
}