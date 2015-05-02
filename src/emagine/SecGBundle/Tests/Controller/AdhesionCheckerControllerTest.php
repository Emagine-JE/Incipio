<?php

namespace emagine\SecGBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdhesionCheckerControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/ca/AdhesionChecker');
    }

    public function testAddcategory()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/ca/AdhesionChecker/addCategory');
    }

}
