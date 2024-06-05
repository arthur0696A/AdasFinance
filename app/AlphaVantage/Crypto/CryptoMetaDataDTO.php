<?php

namespace AdasFinance\AlphaVantage\Crypto;

class CryptoMetaDataDTO
{
    private $information;
    private $currencyCode;
    private $currencyName;
    private $marketCode;
    private $marketName;
    private $lastRefreshed;
    private $timeZone;

    public function __construct(
        $information,
        $currencyCode,
        $currencyName,
        $marketCode,
        $marketName,
        $lastRefreshed,
        $timeZone
    ) {
        $this->information = $information;
        $this->currencyCode = $currencyCode;
        $this->currencyName = $currencyName;
        $this->marketCode = $marketCode;
        $this->marketName = $marketName;
        $this->lastRefreshed = $lastRefreshed;
        $this->timeZone = $timeZone;
    }

    public function getInformation()
    {
        return $this->information;
    }

    public function setInformation($information): void
    {
        $this->information = $information;
    }

    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

    public function setCurrencyCode($currencyCode): void
    {
        $this->currencyCode = $currencyCode;
    }

    public function getCurrencyName()
    {
        return $this->currencyName;
    }

    public function setCurrencyName($currencyName): void
    {
        $this->currencyName = $currencyName;
    }

    public function getMarketCode()
    {
        return $this->marketCode;
    }

    public function setMarketCode($marketCode): void
    {
        $this->marketCode = $marketCode;
    }

    public function getMarketName()
    {
        return $this->marketName;
    }

    public function setMarketName($marketName): void
    {
        $this->marketName = $marketName;
    }

    public function getLastRefreshed()
    {
        return $this->lastRefreshed;
    }

    public function setLastRefreshed($lastRefreshed): void
    {
        $this->lastRefreshed = $lastRefreshed;
    }

    public function getTimeZone()
    {
        return $this->timeZone;
    }

    public function setTimeZone($timeZone): void
    {
        $this->timeZone = $timeZone;
    }

}