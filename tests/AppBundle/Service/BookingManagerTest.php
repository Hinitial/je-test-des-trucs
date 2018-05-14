<?php
/**
 * Created by PhpStorm.
 * User: romain
 * Date: 13/05/18
 * Time: 20:03
 */

namespace Tests\AppBundle\Service;

use AppBundle\Entity\Booking;
use AppBundle\Service\BookingManager;
use PHPUnit\Framework\TestCase;

class BookingManagerTest extends TestCase
{
    public function testVerifyStep(){
        $bookingManager = new BookingManager();

        $booking = new Booking();
    }
}