<?php

namespace Tests\AppBundle\Service;

use PHPUnit\Framework\TestCase;
use AppBundle\Service\YahooFinanceApi;

class YahooFinanceApiTest extends TestCase
{
    public function testSendGetRequest()
    {
        $yahooFinanceApi = new YahooFinanceApi();

        $result = $yahooFinanceApi->getHistoricalData('YHOO', '2017-01-01', '2017-05-01');

        $this->assertNotEmpty($result);

        $this->assertObjectHasAttribute('query', $result);

        $this->assertObjectHasAttribute('results', $result->query);

        $this->assertNotNull($result->query->results);
    }
}