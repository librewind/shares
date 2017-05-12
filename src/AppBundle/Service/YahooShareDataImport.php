<?php

namespace AppBundle\Service;

use AppBundle\Entity\Portfolio;
use \Datetime;

/**
 * Сервис для работы с Yahoo Finance API.
 */
class YahooShareDataImport implements ShareDataImportInterface
{
    /**
     * Временной период, размер которого может переварить сервис Yahoo Finance.
     */
    const PERIOD_PART_SIZE = 17;

    /**
     * Создаёт временные периоды, размер которых может переварить сервис Yahoo Finance.
     *
     * @param int $period
     *
     * @return array
     */
    private function makePeriodParts(int $period) : array
    {
        // Берем на один месяц больше для возможности подсчёта доходности в процентах первого месяца
        // (как разницу в цене между нулеывым и первым месяцем)
        ++$period;

        $result = [];

        $date = new DateTime('first day of this month');

        $endDate = $date->format('Y-m-d');

        if ($period <= self::PERIOD_PART_SIZE) {
            $date->modify('-'.$period.' month');

            $startDate = $date->format('Y-m-d');

            return [
                'startDate' => $startDate,
                'endDate'  => $endDate,
            ];
        }

        $countParts = intval(ceil($period / self::PERIOD_PART_SIZE));

        $lastPeriod = $period % self::PERIOD_PART_SIZE;

        for ($i = 0; $i < $countParts; $i++) {
            if (isset($startDate)) {
                $endDate = $startDate;
            }

            if (($countParts - 1) === $i && $lastPeriod > 0) {
                $date->modify('-'.$lastPeriod.' month');
            } else {
                $date->modify('-'.self::PERIOD_PART_SIZE.' month');
            }

            $startDate = $date->format('Y-m-d');

            $result[] = [
                'startDate' => $startDate,
                'endDate'  => $endDate,
            ];
        }

        return $result;
    }

    /**
     * Отправляет запроса через Curl.
     *
     * @param string $url
     *
     * @return string
     */
    private function sendRequest(string $url) : string
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        return $result;
    }

    /**
     * Получает исторические данные акции от сервиса Yahoo Finance.
     *
     * @param string $symbol
     * @param string $startDate
     * @param string $endDate
     *
     * @return array
     */
    private function getHistoricalData(string $symbol, string $startDate, string $endDate) : array
    {
        $query = urlencode("select * from yahoo.finance.historicaldata where startDate='{$startDate}' and endDate='{$endDate}' and symbol = '{$symbol}'");

        $format = "json";

        $env = urlencode("store://datatables.org/alltableswithkeys");

        $url = "http://query.yahooapis.com/v1/public/yql?env={$env}&format={$format}&q={$query}";

        $result = $this->sendRequest($url);

        $yahoo = json_decode($result);

        return array_reverse($yahoo->query->results->quote);
    }

    /**
     * Группирует по месяцам.
     *
     * @param array $data
     *
     * @return array
     */
    private function groupByMonths(array $data) : array
    {
        $result = [];

        foreach ($data as $item) {
            $date = substr($item->Date, 0, 7);

            if (!isset($result[$date])) {
                $result[$date] = $item->Close;
            }
        }

        ksort($result);

        return $result;
    }

    /**
     * Конвертирует дату в UNIX метку.
     *
     * @param array $data
     *
     * @return array
     */
    private function toUnixTimestamps(array $data) : array
    {
        $result = [];

        foreach($data as $key => $value) {
            $date = strtotime($key . "-01") . "000";

            $result[$date] = $value;
        }

        return $result;
    }

    /**
     * Конвертирует цену в доходность.
     *
     * @param array $data
     *
     * @return array
     */
    private function toYield(array $data) : array
    {
        $result = [];

        foreach ($data as $key => $value) {
            if (isset($prevClose)) {
                $result[$key] = (($value - $prevClose) / $prevClose) * 100;
            }

            $prevClose = $value;
        }

        return $result;
    }

    /**
     * Подсчитывает доходность портфеля по месяцам.
     *
     * @param array $shares
     *
     * @return array
     */
    private function calcYield(array $shares) : array
    {
        $res = [];

        foreach($shares as $share) {
            foreach($share['data'] as $key => $value) {
                if (!isset($res[$key])) {
                    $res[$key] = 0;
                }

                $res[$key] += round($value * $share['proportion'], 2);
            }
        }

        $result = [];

        foreach($res as $key => $value) {
            $result[] = [$key, $value];
        }

        return $result;
    }

    /**
     * Отдаёт доходность портфеля по месяцам.
     *
     * @param Portfolio $portfolio
     * @param int       $period
     *
     * @return array
     *
     * @throws \RuntimeException Когда Yahoo Finance не отвечает
     */
    public function fetchMonthlyYield(Portfolio $portfolio, int $period = 24) : array
    {
        $portfolioShares = $portfolio->getPortfolioShares();

        $periodParts = $this->makePeriodParts($period);

        $inputData = [];

        foreach ($portfolioShares as $portfolioShare) {
            $data = [];

            $symbol = $portfolioShare->getShare()->getSymbol();

            foreach ($periodParts as $periodPart) {
                $data = array_merge($data, $this->getHistoricalData($symbol, $periodPart['startDate'], $periodPart['endDate']));
            }

            if (is_null($data)) {
                throw new \RuntimeException('Yahoo Finance does not response');
            }

            $data = $this->groupByMonths($data);

            $data = $this->toYield($data);

            $data = $this->toUnixTimestamps($data);

            $inputData[] = [
                'data'       => $data,
                'proportion' => $portfolioShare->getProportion(),
            ];
        }

        $result = $this->calcYield($inputData);

        $endDate = end($result)[0];

        $startDate = reset($result)[0];

        return [
            'error'     => false,
            'data'      => $result,
            'endDate'   => $endDate,
            'startDate' => $startDate,
        ];
    }
}