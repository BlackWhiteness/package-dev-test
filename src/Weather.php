<?php

namespace Wangshuai\Weather;

use GuzzleHttp\Client;
use Wangshuai\Weather\Exceptions\InvalidArgumentException;
use Wangshuai\Weather\Exceptions\HttpException;


class Weather
{
    protected $key;

    protected $guzzleOptions = [];

    public function __construct(string $key)
    {
        $this->key = $key;
    }


    public function getHttpClient()
    {
        return new Client($this->guzzleOptions);
    }

    public function setGuzzleOptions($option)
    {
        $this->guzzleOptions = $option;
    }


    public function getWeather($city, string $type = 'base', string $format = 'json')
    {
        if (!in_array($type, ['base', 'all'])) {
            throw  new InvalidArgumentException('Invalid response format: ' . $type);
        }

        if (!in_array($format, ['json', 'xml'])) {
            throw new InvalidArgumentException('Invalid response format: ' . $format);
        }


        $url = 'https://restapi.amap.com/v3/weather/weatherInfo';

        $query = array_filter([
            'key' => $this->key,
            'city' => $city,
            'extensions' => $type,
            'output' => $format
        ]);

        try {

            $response = $this->getHttpClient()->get($url, ['query' => $query])->getBody()->getContents();

            return 'json' === $format ? \json_decode($response, true) : $response;
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }


    }

}