<?php

namespace AppBundle\Service;

use AppBundle\Entity\Portfolio;

/**
 * Класс, преобразующий котировки акций в доходность по месяцам.
 */
class QuoteToProfitTransform
{
    /**
     * Объект, подготавливающий данные к запросам к сервису Yahoo Finance.
     *
     * @var YahooFinancePrepare
     */
    private $yahooFinancePrepare;

    /**
     * Конструктор QuoteToProfitTransform.
     */
    public function __construct()
    {
        $this->yahooFinancePrepare = new YahooFinancePrepare();
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
     * Конвертирует цену в доходность.
     *
     * @param array $data
     *
     * @return array
     */
    private function toProfit(array $data) : array
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
     * Получает исторические котировки акций портфеля от сервиса Yahoo Finance.
     *
     * @param Portfolio $portfolio
     * @param int       $periodInMonths
     *
     * @return array
     */
    public function getHistoricalData(Portfolio $portfolio, int $periodInMonths) : array
    {
        $shares = $this->yahooFinancePrepare->getHistoricalData($portfolio, $periodInMonths);

        foreach ($shares as $symbol => &$data) {
            $data = $this->groupByMonths($data);
            $data = $this->toProfit($data);
            $data = $this->toUnixTimestamps($data);
        }

        return $shares;
    }
}