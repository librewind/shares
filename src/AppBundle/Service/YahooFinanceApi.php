<?php

namespace AppBundle\Service;

use \stdClass;

class YahooFinanceApi
{
    /**
     * Http клиент.
     *
     * @var HttpClient
     */
    private $httpClient;

    /**
     * Конструктор YahooFinanceApi.
     *
     * @param HttpClient $httpClient
     */
    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Получает исторические котировки акции от сервиса Yahoo Finance.
     *
     * @param string $symbol
     * @param string $startDate
     * @param string $endDate
     *
     * @return stdClass
     */
    public function getHistoricalData(string $symbol, string $startDate, string $endDate) : stdClass
    {
        $url = 'http://query.yahooapis.com/v1/public/yql';

        $params['q'] = "select * from yahoo.finance.historicaldata where startDate='{$startDate}' and endDate='{$endDate}' and symbol = '{$symbol}'";
        $params['format'] = "json";
        $params['env'] = "store://datatables.org/alltableswithkeys";

        $result = $this->httpClient->sendGetRequest($url, $params);

        return json_decode($result);
    }
}