<?php
/**
 * Created by PhpStorm.
 * User: romain
 * Date: 10/04/18
 * Time: 12:30
 */

namespace AppBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class NoSunday extends Constraint
{
    public $message = 'Impossible de commander en ligne pour un dimanche';
}