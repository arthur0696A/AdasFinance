<?php
namespace AdasFinance\Service;

class AlphaVantageApiService
{
    //private const API_KEY = 'D6QHBA0GGE0H468N';
    private const API_KEY = 'demo';
    private const API_BASE_URL = 'https://www.alphavantage.co/';

    public static function searchByFilter($symbol)
    {
        $symbol = 'tesco';


        $url = self::API_BASE_URL . 'query?function=SYMBOL_SEARCH&keywords=' . urlencode($symbol) . '&apikey=' . self::API_KEY;
        $response = file_get_contents($url);
        $data = json_decode($response, true);

        return $data;
    }
}