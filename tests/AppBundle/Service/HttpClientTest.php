<?php

namespace Tests\AppBundle\Service;

use PHPUnit\Framework\TestCase;
use AppBundle\Service\HttpClient;

class HttpClientTest extends TestCase
{
    public function testSendGetRequest()
    {
        $httpClient = new HttpClient();

        $result = $httpClient->sendGetRequest('http://query.yahooapis.com/v1/public/yql');

        $this->assertNotFalse($result);
    }
}