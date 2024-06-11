<?php

namespace AdasFinance\AlphaVantage;

use AdasFinance\AlphaVantage\Ibovespa\ChartDataDTO;
use AdasFinance\AlphaVantage\Ibovespa\GlobalQuoteDTO;

class AlphaVantageApiService
{
    private const API_KEY = 'D6QHBA0GGE0H468N';
    // private const API_KEY = 'demo';
    private const API_BASE_URL = 'https://www.alphavantage.co/';

    public static function searchByFilter($symbol)
    {
        $symbol = 'tesco';

        $url = self::API_BASE_URL . 'query?function=SYMBOL_SEARCH&keywords=' . urlencode(
                $symbol
            ) . '&apikey=' . self::API_KEY;
        $response = file_get_contents($url);
        $data = json_decode($response, true);

        return $data;
    }

    public static function getLastPrice($asset)
    {
        if ($asset->getGroupType() == 3) {
            return self::getDigitalCurrencyMonthly($asset->getSymbol());
        }

        return self::getGlobalQuote($asset->getSymbol());
    }

    private static function getGlobalQuote($symbol)
    {
        $symbol .= ".SAO";
        $url = self::API_BASE_URL . 'query?function=GLOBAL_QUOTE&symbol=' . urlencode(
                $symbol
            ) . '&apikey=' . self::API_KEY;
        $response = file_get_contents($url);
        $data = json_decode($response, true);

        $globalQuoteDto = GlobalQuoteDTO::fromArray($data);

        return $globalQuoteDto->getPrice();
    }

    private static function getDigitalCurrencyMonthly($symbol)
    {
        $url = self::API_BASE_URL . 'query?function=DIGITAL_CURRENCY_MONTHLY&symbol=' . urlencode(
                $symbol
            ) . '&market=BRL&apikey=' . self::API_KEY;

        $response = file_get_contents($url);
        $data = json_decode($response, true);

        $cryptoCurrencyDto = CryptoCurrencyDTO::fromArray($data);

        return $cryptoCurrencyDto->getTimeSeriesDto()->getClose();
    }

    public static function getChartHistory($symbol)
    {
        $url = self::API_BASE_URL . 'query?function=TIME_SERIES_MONTHLY&symbol=' . urlencode(
                $symbol
            ) . '.SAO&apikey=' . self::API_KEY;

        $response = file_get_contents($url);

        $encodedResponse = json_encode($response);
        $chartDataDTO = @ChartDataDTO::convertToDTOs($encodedResponse);

        return $chartDataDTO;
    }

}