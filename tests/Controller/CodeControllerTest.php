<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CodeControllerTest extends WebTestCase
{
    public function testVerifPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/verification');
        $this->assertResponseIsSuccessful();
        $form = $crawler->selectButton('Chercher')->form();
        $form['code_search[code]'] = 'INVALIDCODE';
        $client->submit($form);
        $this->assertSelectorTextContains('.msg-n', 'Code de réservation incorrect.');
        $form['code_search[code]'] = '94E4FB4A6E336413';
        $client->submit($form);
        $this->assertResponseRedirects('/reservation/94E4FB4A6E336413', 302);
    }

    public function testRecapPage()
    {
        $client = static::createClient();
        $client->request('GET', '/reservation/94E4FB4A6E336413');
        $this->assertResponseIsSuccessful();
    }
}
