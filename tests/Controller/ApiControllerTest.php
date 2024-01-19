<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiControllerTest extends WebTestCase
{
    public function testGetResaDetailsByInvalidCodeLength()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/reservation/INVALIDCODELENGTH');
        $this->assertResponseStatusCodeSame(403);
        $response = $client->getResponse();
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(false, $responseData['success']);
        $this->assertEquals('Invalid code length', $responseData['message']);
    }

    public function testGetResaDetailsByNonExistingCode()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/reservation/NONEXISTINGCODE1');
        $this->assertResponseStatusCodeSame(404);
        $response = $client->getResponse();
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(false, $responseData['success']);
        $this->assertEquals('Reservation not found', $responseData['message']);
    }

    public function testGetResaDetailsByValidCode()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/reservation/A20422EBE98B3A38');
        $this->assertResponseIsSuccessful();
        $response = $client->getResponse();
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(true, $responseData['success']);
        $this->assertEquals(200, $responseData['status']);
    }
}
