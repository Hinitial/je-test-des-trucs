<?php
/**
 * Created by PhpStorm.
 * User: romain
 * Date: 24/04/18
 * Time: 12:48
 */

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class NotOverThousand extends Constraint
{
    public $message = 'Plus de billets disponible pour la date sélectionné';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}