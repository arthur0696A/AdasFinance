<?php

namespace AdasFinance\AlphaVantage\Ibovespa;

class MetaDataDTO
{
    private $information;
    private $symbol;
    private $lastRefreshed;
    private $timeZone;

    public function __construct($information, $symbol, $lastRefreshed, $timeZone)
    {
        $this->information = $information;
        $this->symbol = $symbol;
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

    public function getSymbol()
    {
        return $this->symbol;
    }

    public function setSymbol($symbol): void
    {
        $this->symbol = $symbol;
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

