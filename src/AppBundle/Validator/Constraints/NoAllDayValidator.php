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
    const LIMIT_HOUR = 14;

    public function validate($booking, Constraint $constraint)
    {
        $now = new \DateTime();
        $date_now = $now->format('d/m/Y');
        $hour_now = (int) $now->format('G');

        $visitDate = $booking->getVisitDate();
        $visitDate = $visitDate->format('d/m/Y');

        if(($booking->getTicketType() === true) && ($visitDate == $date_now) && ($hour_now >= self::LIMIT_HOUR)){
            $this->context->buildViolation($constraint->message)
//                ->atPath('typeBillet')
                ->addViolation();
        }
    }
}