<?php
/**
 * Created by PhpStorm.
 * User: romain
 * Date: 23/04/18
 * Time: 23:00
 */

namespace AppBundle\Exceptions;


class SessionNotFoundException extends \Exception
{
    public function __construct($message, $code = 0)
    {
        parent::__construct($message, $code);
    }

    public function __toString()
    {
        return $this->message;
    }
}