<?php

namespace AppBundle\Service;

use AppBundle\Entity\Portfolio;

/**
 * Класс, подсчитывающий доходность всего портфеля.
 */
class PortfolioProfit implements PortfolioProfitInterface
{
    /**
     * Объект, преобразующий котировки акций в доходность по месяцам.
     *
     * @var QuoteToProfitTransform
     */
    private $quoteToProfitTransform;

    /**
     * Конструктор PortfolioProfit.
     */
    public function __construct()
    {
        $this->quoteToProfitTransform = new QuoteToProfitTransform();
    }

    /**
     * Подсчитывает доходность портфеля по месяцам.
     *
     * @param array $shares
     *
     * @return array
     */
    private function calcProfit(array $shares, array $ratios) : array
    {
        $res = [];

        foreach($shares as $symbol => $data) {
            foreach($data as $key => $value) {
                if (!isset($res[$key])) {
                    $res[$key] = 0;
                }

                $res[$key] += round($value * $ratios[$symbol], 2);
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
     * @param int       $periodInMonths
     *
     * @return array
     */
    public function getMonthlyProfit(Portfolio $portfolio, int $periodInMonths = self::DEFAULT_PERIOD) : array
    {
        $portfolioShares = $portfolio->getPortfolioShares();

        $ratios = [];
        foreach ($portfolioShares as $portfolioShare) {
            $symbol = $portfolioShare->getShare()->getSymbol();
            $ratios[$symbol] = $portfolioShare->getRatio();
        }

        $shares = $this->quoteToProfitTransform->getHistoricalData($portfolio, $periodInMonths);

        $profit = $this->calcProfit($shares, $ratios);

        $endDate = end($profit)[0];

        $startDate = reset($profit)[0];

        return [
            'error'     => false,
            'data'      => $profit,
            'endDate'   => $endDate,
            'startDate' => $startDate,
        ];
    }
}