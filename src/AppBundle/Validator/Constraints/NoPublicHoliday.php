<?php
/**
 * Created by PhpStorm.
 * User: romain
 * Date: 02/05/18
 * Time: 23:03
 */

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class NoPublicHoliday extends Constraint
{
    public $message = 'Impossible de commander pour un jour férié';
}