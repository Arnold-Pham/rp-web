<?php

namespace App\Tests\Security;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminAccessTest extends WebTestCase
{
    public function testAccessToAdminPages()
    {
        $client = static::createClient();
        $client->request('GET', '/admin');
        $this->assertResponseRedirects('/login');
        $crawler = $client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Connexion');
        $client->request('GET', '/admin/airport');
        $this->assertResponseRedirects('/login');
        $crawler = $client->followRedirect();
        $client->request('GET', '/admin/parking');
        $this->assertResponseRedirects('/login');
        $crawler = $client->followRedirect();
        $client->request('GET', '/admin/place');
        $this->assertResponseRedirects('/login');
        $crawler = $client->followRedirect();
        $client->request('GET', '/admin/resa');
        $this->assertResponseRedirects('/login');
        $crawler = $client->followRedirect();
        $client->request('GET', '/admin/infos');
        $this->assertResponseRedirects('/login');
        $crawler = $client->followRedirect();
        $client->request('GET', '/admin/voiture');
        $this->assertResponseRedirects('/login');
        $crawler = $client->followRedirect();
        $client->request('GET', '/admin/adresse');
        $this->assertResponseRedirects('/login');
        $crawler = $client->followRedirect();
    }
}
