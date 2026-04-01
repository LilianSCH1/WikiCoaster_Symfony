<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CoasterControllerTest extends WebTestCase
{
    // Les méthode de test doivent être préfixée par 'test'
    public function testIndex(): void
    {
        $client = self::createClient();

        // Charge la page de la liste des coasters
        $client->request('GET', '/coaster');

        // Test si la page n'a pas d'erreur
        $this->assertResponseIsSuccessful('La page index des coasters contient une erreur');
    }
}
