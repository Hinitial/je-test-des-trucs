<?php
/**
 * Created by PhpStorm.
 * User: romain
 * Date: 17/04/18
 * Time: 10:39
 */

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class NoAllDay extends Constraint
{
    public $message = 'Impossible de prendre un billet "Journée" pour le jour meme après 14h ';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}