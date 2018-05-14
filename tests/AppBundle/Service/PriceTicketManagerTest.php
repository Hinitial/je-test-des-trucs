<?php
/**
 * Created by PhpStorm.
 * User: romain
 * Date: 06/05/18
 * Time: 11:13
 */

namespace Tests\AppBundle\Service;


use PHPUnit\Framework\TestCase;

class PriceTicketManagerTest extends TestCase
{
    /**
     * @dataProvider dataForPriceTicket
     * @param $age
     * @param $reducPrice
     * @param $expectedPrice
     */
    public function testPriceTicketManager($age, $reducPrice, $expectedPrice){

        $priceTicketManager = new \AppBundle\Service\PriceTicketManager();

        $ticket = $this
            ->getMockBuilder('AppBundle\Entity\Ticket')
            ->getMock();

        $ticket
            ->method('getAge')
            ->willReturn($age);

        $ticket
            ->method('getReducPrice')
            ->willReturn($reducPrice);

        $this->assertSame($expectedPrice,$priceTicketManager->getTicketPrice($ticket));
    }

    public function dataForPriceTicket(){
        return[
            [21, false, 16],
            [21, true, 10],
            [10, true, 8],
            [4, true, 0],
            [60, true, 10],
            [60, false, 12]
        ];
    }
}