<?php
/**
 * Created by PhpStorm.
 * User: romain
 * Date: 06/05/18
 * Time: 22:21
 */

namespace Tests\AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TicketingControllerTest extends WebTestCase
{
    public function testRedirectionToFirstStep(){
        $client = static::createClient();
//        $this->expectException('SessionNotFoundException');
        $client->request('GET', 'musee-louvre/billetterie/information');

        $this->assertSame(302, $client->getResponse()->getStatusCode());
    }

    public function testFirstStepSubmint(){
        $client = static::createClient();
        $crawder = $client->request('GET', 'musee-louvre/billetterie');
        $form = $crawder->selectButton('reservation[nextStep]')->form();

        $form['reservation[visitDate]'] = '2018/05/23';
        $form['reservation[ticketType]'] = true;
        $form['reservation[ticketNumber]'] = 1;
        $form['reservation[email]'] = 'romsy2111@hotmail.fr';

        $crawder = $client->submit($form);

        $client->followRedirect();

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testTooMutchTicket(){
        $client = static::createClient();
        $crawder = $client->request('GET', 'musee-louvre/billetterie');
        $form = $crawder->selectButton('reservation[nextStep]')->form();

        $form['reservation[visitDate]'] = '2018/05/23';
        $form['reservation[ticketType]'] = true;
        $form['reservation[ticketNumber]'] = 8;
        $form['reservation[email]'] = 'romsy2111@hotmail.fr';

        $crawder = $client->submit($form);

        $error = $crawder->filter('.form-error-message')->text();

        $this->assertSame("Vous ne pouvez pas acheter plus de \"7\" billets en une seul fois.", $error);
    }

//    public function testThrowingStepException(){
//        $client = static::createClient();
//        $client->request('GET', 'musee-louvre/billetterie');
//        $this->assertSame(200, $client->getResponse()->getStatusCode());
//
//        $client->request('GET', 'musee-louvre/billetterie/information');
//        $this->assertSame(500, $client->getResponse()->getStatusCode());
//    }
}