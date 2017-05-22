<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PortfolioControllerTest extends WebTestCase
{
    public function testCompleteScenario()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'test',
            'PHP_AUTH_PW'   => '123456',
        ]);

        // Create a new entry in the database
        $crawler = $client->request('GET', '/portfolio/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /post/");
        $text = static::$kernel->getContainer()->get('translator')->trans('button.add', [], 'labels');
        $crawler = $client->click($crawler->selectLink($text)->link());

        // Заполнение формы и отправка её
        $text = static::$kernel->getContainer()->get('translator')->trans('form.save', [], 'labels');
        $form = $crawler->selectButton($text)->form(array(
            'appbundle_portfolio[name]'  => 'Test',
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("Test")')->count(), 'Missing element td:contains("Test")');

        // Edit the entity
        $text = static::$kernel->getContainer()->get('translator')->trans('button.edit', [], 'labels');
        $crawler = $client->click($crawler->selectLink($text)->link());

        $text = static::$kernel->getContainer()->get('translator')->trans('form.save', [], 'labels');
        $form = $crawler->selectButton($text)->form(array(
            'appbundle_portfolio[name]'  => 'Foo',
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("Foo")')->count(), 'Missing element td:contains("Foo")');

        // Delete the entity
        $text = static::$kernel->getContainer()->get('translator')->trans('button.delete', [], 'labels');
        $client->submit($crawler->selectButton($text)->form());
        $crawler = $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/Foo/', $client->getResponse()->getContent());
    }
}