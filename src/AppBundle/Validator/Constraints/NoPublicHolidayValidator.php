<?php
/**
 * Created by PhpStorm.
 * User: romain
 * Date: 02/05/18
 * Time: 23:04
 */

namespace AppBundle\Validator\Constraints;

use AppBundle\Service\PublicHolidayManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NoPublicHolidayValidator extends ConstraintValidator
{
    protected $holidayManager;

    public function __construct(PublicHolidayManager $holidayManager){
        $this->holidayManager = $holidayManager;
    }

    public function validate($value, Constraint $constraint)
    {
        $currentDate = new \DateTime();
        $currentYear = $currentDate->format('Y');
        if(in_array($value,$this->holidayManager->getPublicHolidayTab($currentYear))){
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}