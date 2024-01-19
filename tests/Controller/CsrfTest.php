<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CsrfTest extends WebTestCase
{
    public function testInvalidCsrfToken()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Connexion')->form();
        $client->submit($form, [
            'email' => 'a',
            'password' => 'a',
            '_csrf_token' => 'test'
        ]);
        $this->assertResponseRedirects('/login');
    }
}
