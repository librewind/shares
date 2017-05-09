<?php

namespace AppBundle\Service;

class YahooShareDataImport implements ShareDataImportInterface
{
    protected function getData($shares, $period)
    {
        /*$ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, API_PROTOCOL . "://" . API_HOST . "/api/v2/accounts/rewardRequest");

        curl_setopt($ch, CURLOPT_HEADER, false);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);

        curl_setopt($ch, CURLOPT_USERAGENT, 'ARTAPI');

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, "userId=" . $userId . "&requestId=" . $requestId);

        $json_result = curl_exec($ch);

        $result = json_decode($json_result);

        curl_close($ch);

        if ($result->status == 0) {
            return true;
        }

        return false;*/

        return [
            'KO' => [
                ['date'  => '1430438400000', 'yield' => 23],
                ['date'  => '1433116800000', 'yield' => 41],
                ['date'  => '1435708800000', 'yield' => 12],
                ['date'  => '1438387200000', 'yield' => 22],
                ['date'  => '1441065600000', 'yield' => -3],
                ['date'  => '1443657600000', 'yield' => 12],
                ['date'  => '1446336000000', 'yield' => 22],
                ['date'  => '1448928000000', 'yield' => 7],
                ['date'  => '1451606400000', 'yield' => 3],
                ['date'  => '1454284800000', 'yield' => -6],
                ['date'  => '1456790400000', 'yield' => 23],
                ['date'  => '1459468800000', 'yield' => 34],
                ['date'  => '1462060800000', 'yield' => 2],
                ['date'  => '1464739200000', 'yield' => -34],
                ['date'  => '1467331200000', 'yield' => 22],
                ['date'  => '1470009600000', 'yield' => 32],
                ['date'  => '1472688000000', 'yield' => -42],
                ['date'  => '1475280000000', 'yield' => 23],
                ['date'  => '1477958400000', 'yield' => 12],
                ['date'  => '1480550400000', 'yield' => 22],
                ['date'  => '1483228800000', 'yield' => 43],
                ['date'  => '1485907200000', 'yield' => 12],
                ['date'  => '1488326400000', 'yield' => -5],
                ['date'  => '1491004800000', 'yield' => 42],
            ],
            'YHOO' => [
                ['date'  => '1430438400000', 'yield' => 32],
                ['date'  => '1433116800000', 'yield' => 42],
                ['date'  => '1435708800000', 'yield' => 12],
                ['date'  => '1438387200000', 'yield' => 53],
                ['date'  => '1441065600000', 'yield' => 23],
                ['date'  => '1443657600000', 'yield' => 63],
                ['date'  => '1446336000000', 'yield' => 53],
                ['date'  => '1448928000000', 'yield' => 23],
                ['date'  => '1451606400000', 'yield' => 53],
                ['date'  => '1454284800000', 'yield' => 12],
                ['date'  => '1456790400000', 'yield' => 7],
                ['date'  => '1459468800000', 'yield' => 3],
                ['date'  => '1462060800000', 'yield' => 9],
                ['date'  => '1464739200000', 'yield' => -2],
                ['date'  => '1467331200000', 'yield' => 34],
                ['date'  => '1470009600000', 'yield' => 19],
                ['date'  => '1472688000000', 'yield' => 41],
                ['date'  => '1475280000000', 'yield' => 32],
                ['date'  => '1477958400000', 'yield' => 43],
                ['date'  => '1480550400000', 'yield' => 21],
                ['date'  => '1483228800000', 'yield' => 56],
                ['date'  => '1485907200000', 'yield' => 21],
                ['date'  => '1488326400000', 'yield' => 32],
                ['date'  => '1491004800000', 'yield' => -3],
            ]
        ];
    }

    public function fetchYield($shares, $period)
    {
        $data = $this->getData($shares, $period);

        $result = [];

        foreach ($data as $key => $item) {
            foreach ($item as $item2) {
                if (! isset($result[$key][$item2['date']])) {
                    $result[$item2['date']] = 0;
                }

                $result[$item2['date']] += $shares[$key] * $item2['yield'];
            }
        }

        return $result;
    }
}