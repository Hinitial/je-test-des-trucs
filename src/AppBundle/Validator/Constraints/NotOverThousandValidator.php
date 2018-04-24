<?php
/**
 * Created by PhpStorm.
 * User: romain
 * Date: 24/04/18
 * Time: 12:50
 */

namespace AppBundle\Validator\Constraints;

use AppBundle\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NotOverThousandValidator extends ConstraintValidator
{
    const LIMIT_PER_DAY = 1000;

    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    public function validate($reservation, Constraint $constraint)
    {
        $jourVisiste = $reservation->getJourVisite();
        $nbreBillet = $reservation->getNbreBillet();

        $count = $this->entityManager->getRepository('AppBundle:Reservation')
            ->getNombreBillet($jourVisiste->format('Y-m-d H:i:s'));

        if(($count + $nbreBillet) > self::LIMIT_PER_DAY){
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}