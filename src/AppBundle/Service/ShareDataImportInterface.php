<?php

namespace AppBundle\Service;

use AppBundle\Entity\Portfolio;

interface ShareDataImportInterface
{
    /**
     * Отдаёт доходность портфеля по месяцам.
     *
     * @param Portfolio $portfolio
     * @param int       $period
     *
     * @return array
     */
    public function fetchMonthlyYield(Portfolio $portfolio, int $period = 24) : array;
}