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
    public function testTicketingIsUp(){
        $client = static::createClient();
        $client->request('GET', 'fr/billetterie');

//        echo $client->getResponse()->getContent();
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testRedirectionToFirstStep(){
        $client = static::createClient();
        $client->request('GET', 'fr/billetterie/information');

        $this->assertSame(302, $client->getResponse()->getStatusCode());
    }

    public function testBeginBooking(){
        $client = static::createClient();
        $crawler = $client->request('GET', 'fr/billetterie');

        $form = $crawler->selectButton('reservation[nextStep]')->form();

        $form['reservation[visitDate]'] = '2018/05/23';
        $form['reservation[ticketType]'] = true;
        $form['reservation[ticketNumber]'] = 1;
        $form['reservation[email]'] = 'romsy2111@hotmail.fr';

        $crawler = $client->submit($form);

        $client->followRedirect();

        $this->assertSame(200, $client->getResponse()->getStatusCode());

        $crawler = $client->getCrawler();
        $form2 = $crawler->selectButton('information[payment]')->form();

            $form2['information[tickets][0][name]'] = 'Alvarez';
            $form2['information[tickets][0][firstName]'] = 'Romain';
            $form2['information[tickets][0][country]'] = 'FR';
            $form2['information[tickets][0][birthDate]'] = '1996/11/21';
            $form2['information[tickets][0][reducPrice]'] = false;

        $crawler = $client->submit($form2);

        $client->followRedirect();

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testTooMutchTicket(){
        $client = static::createClient();
        $crawder = $client->request('GET', 'fr/billetterie');

        $form = $crawder->selectButton('reservation[nextStep]')->form();

        $form['reservation[visitDate]'] = '2018/05/23';
        $form['reservation[ticketType]'] = true;
        $form['reservation[ticketNumber]'] = 8;
        $form['reservation[email]'] = 'romsy2111@hotmail.fr';

        $crawder = $client->submit($form);

        $error = $crawder->filter('.form-error-message')->text();

        $this->assertSame("Vous ne pouvez pas acheter plus de \"7\" billets en une seul fois.", $error);
    }
}