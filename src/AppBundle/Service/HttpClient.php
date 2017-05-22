<?php

namespace AppBundle\Service;

/**
 * Класс для работы с cURL.
 */
class HttpClient
{
    /**
     * Сеанс cURL.
     *
     * @var resource
     */
    private $curl;

    /**
     * Конструктор Curl.
     */
    public function __construct()
    {
        $this->curl = curl_init();
    }

    /**
     * Отправляет GET запрос.
     *
     * @param string $url
     * @param array|null $params
     * @return string
     */
    public function sendGetRequest(string $url, array $params = null)
    {
        if (!empty($params)) {
            $url = $url.'?'.http_build_query($params);
        }

        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($this->curl);

        return $result;
    }

    /**
     * Завершает сеанс cURL.
     */
    public function close()
    {
        if (is_resource($this->curl)) {
            curl_close($this->curl);
        }
    }
}