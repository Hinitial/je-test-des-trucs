<?php
/**
 * Created by PhpStorm.
 * User: romain
 * Date: 06/05/18
 * Time: 20:52
 */

namespace Tests\AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AppControllerTest extends WebTestCase
{
    public function testHomeIsUp(){
        $client = static::createClient();
        $client->request('GET', 'fr/');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testContactIsUp(){
        $client = static::createClient();
        $client->request('GET', 'fr/contact');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }
}