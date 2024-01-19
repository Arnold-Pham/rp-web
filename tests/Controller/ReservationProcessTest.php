<?php

namespace App\Tests\Controller;

use Faker\Factory;
use Symfony\Component\Panther\PantherTestCase;

class ReservationProcessTest extends PantherTestCase
{
    public function testCompleteReservationProcess()
    {
        for ($i = 0; $i < 15; $i++) {
            $faker = Factory::create();
            $client = static::createPantherClient();

            $crawler = $client->request('GET', '/reservation');
            $form = $crawler->selectButton('Envoyer')->form([
                'reservation[email]' => $faker->email(),
                'reservation[dateA]' => "18-01-2024T\t15:00",
                'reservation[flightA]' => $faker->regexify('[A-Z]{3}[0-9]{3}'),
                'reservation[dateB]' => "20-01-2024T\t16:00",
                'reservation[flightB]' => $faker->regexify('[A-Z]{3}[0-9]{3}'),
                'reservation[airport]' => $faker->numberBetween(1, 3),
                'reservation[valet]' => true,
                'reservation[extra2]' => true,
                'reservation[extra3]' => true,
            ]);
            $client->submit($form);
            $this->assertPageTitleContains('Information');

            $form = $client->getCrawler()->selectButton('Envoyer')->form([
                'personal_data[type]' => 0,
                'personal_data[genre]' => $faker->numberBetween(0, 1),
                'personal_data[firstname]' => $faker->firstName(),
                'personal_data[lastname]' => $faker->lastName(),
                'personal_data[phoneNumber]' => $faker->numberBetween(600000000, 799999999)
            ]);
            $client->submit($form);
            $this->assertPageTitleContains('Voiture');

            $form = $client->getCrawler()->selectButton('Envoyer')->form([
                'car[brand]' => $faker->regexify('[a-zA-Z]{9}'),
                'car[model]' => $faker->regexify('[a-zA-Z]{9}'),
                'car[color]' => '#' . $faker->regexify('[a-f1-9]{6}'),
                'car[plate]' => $faker->regexify('[A-Z]{2}-[0-9]{3}-[A-Z]{2}')
            ]);
            $client->submit($form);
            $this->assertPageTitleContains('Adresse');

            $form = $client->getCrawler()->selectButton('Envoyer')->form([
                'address[address]' => $faker->address(),
                'address[city]' => $faker->city(),
                'address[zipCode]' => $faker->numberBetween(10000, 95000),
                'address[diff]' => true
            ]);
            $client->submit($form);
            $this->assertPageTitleContains('Adresse de Facturation');

            $form = $client->getCrawler()->selectButton('Envoyer')->form([
                'address_invoice[address]' => $faker->address(),
                'address_invoice[city]' => $faker->city(),
                'address_invoice[zipCode]' => $faker->numberBetween(10000, 95000),
            ]);
            $client->submit($form);
            $this->assertPageTitleContains('Paiement');
        }
    }
}
