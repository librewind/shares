<?php

namespace AppBundle\Service;

use AppBundle\Entity\Portfolio;
use \DateTime;

/**
 * Класс, подготавливающий данные к запросам к сервису Yahoo Finance.
 */
class YahooFinancePrepare
{
    /**
     * Период по умолчанию (в месяцах).
     */
    const DEFAULT_PERIOD = 24;

    /**
     * Временной период, размер которого может переварить сервис Yahoo Finance.
     */
    const PERIOD_PART_SIZE = 17;

    /**
     * Объект для работы с Yahoo Finance API.
     *
     * @var YahooFinanceApi
     */
    private $yahooFinanceApi;

    /**
     * Конструтор YahooFinancePrepare.
     */
    public function __construct()
    {
        $this->yahooFinanceApi = new YahooFinanceApi;
    }

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
     * Получает исторические котировки акций портфеля от сервиса Yahoo Finance.
     *
     * @param Portfolio $portfolio
     * @param int       $periodInMonths
     *
     * @return array
     *
     * @throws \RuntimeException Когда Yahoo Finance не отвечает
     */
    public function getHistoricalData(Portfolio $portfolio, int $periodInMonths = self::DEFAULT_PERIOD) : array
    {
        $result = [];

        $portfolioShares = $portfolio->getPortfolioShares();
        $periodParts = $this->makePeriodParts($periodInMonths);

        foreach ($portfolioShares as $portfolioShare) {
            $symbol = $portfolioShare->getShare()->getSymbol();

            $data = [];
            foreach ($periodParts as $periodPart) {
                $yahoo = $this->yahooFinanceApi->getHistoricalData($symbol, $periodPart['startDate'], $periodPart['endDate']);

                if (!isset($yahoo->query->results->quote)) {
                    throw new \RuntimeException('Yahoo Finance does not response');
                }

                $quote = array_reverse($yahoo->query->results->quote);
                $data = array_merge($data, $quote);
            }

            $result[$symbol] = $data;
        }

        return $result;
    }
}