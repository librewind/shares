<?php

namespace AppBundle\Service;

use AppBundle\Entity\Portfolio;

interface ShareDataImportInterface
{
    public function fetchMonthlyYield(Portfolio $portfolio, int $period = 24);
}