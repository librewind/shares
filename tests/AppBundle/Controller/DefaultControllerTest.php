<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'test',
            'PHP_AUTH_PW'   => '123456',
        ]);

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $text = static::$kernel->getContainer()->get('translator')->trans('welcome');
        $this->assertContains(
            $text,
            $client->getResponse()->getContent()
        );
    }
}
