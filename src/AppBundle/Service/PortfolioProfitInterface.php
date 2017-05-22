<?php

namespace AppBundle\Service;

use AppBundle\Entity\Portfolio;

interface PortfolioProfitInterface
{
    /**
     * Период в месяцах по умолчанию по которому рассчитывается доходность.
     */
    const DEFAULT_PERIOD = 24;

    /**
     * Отдаёт доходность портфеля по месяцам.
     *
     * @param Portfolio $portfolio
     * @param int       $periodInMonths
     *
     * @return array
     */
    public function getMonthlyProfit(Portfolio $portfolio, int $periodInMonths = self::DEFAULT_PERIOD) : array;
}