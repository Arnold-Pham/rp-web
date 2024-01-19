<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MainControllerTest extends WebTestCase
{
    public function testContactPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact');
        $this->assertResponseIsSuccessful();
        $form = $crawler->selectButton('Envoyer')->form([
            'contact[email]' => 'test@example.com',
            'contact[sujet]' => 'Sujet du Test',
            'contact[texte]' => 'Ceci est un message de test.'
        ]);
        $client->submit($form);
        $this->assertResponseRedirects('/contact', 303);
        $crawler = $client->followRedirect();
        $this->assertSelectorTextContains('.msg-y', 'Message envoyé avec succès.');
    }
}
