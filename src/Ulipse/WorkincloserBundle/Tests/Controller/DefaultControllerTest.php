<?php

namespace Ulipse\WorkincloserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertFalse($crawler->filter('html:contains("Your Addresses")')->count() > 0);
    }
}
