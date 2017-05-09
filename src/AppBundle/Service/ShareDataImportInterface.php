<?php

namespace AppBundle\Service;

interface ShareDataImportInterface
{
    public function fetchYield($shares, $period);
}