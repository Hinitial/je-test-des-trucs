<?php
/**
 * Created by PhpStorm.
 * User: romain
 * Date: 29/04/18
 * Time: 12:51
 */

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class TooFarFuture extends Constraint
{
    public $message = 'La date "{{ visitDate }}" est trop loin dans le temps';
}