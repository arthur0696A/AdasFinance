<?php

namespace AdasFinance\AlphaVantage\Ibovespa;

class TimeSeriesDTO
{
    private $date;
    private $open;
    private $high;
    private $low;
    private $close;
    private $volume;

    public function __construct($date, $open, $high, $low, $close, $volume)
    {
        $this->date = $date;
        $this->open = $open;
        $this->high = $high;
        $this->low = $low;
        $this->close = $close;
        $this->volume = $volume;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date): void
    {
        $this->date = $date;
    }

    public function getOpen()
    {
        return $this->open;
    }

    public function setOpen($open): void
    {
        $this->open = $open;
    }

    public function getHigh()
    {
        return $this->high;
    }

    public function setHigh($high): void
    {
        $this->high = $high;
    }

    public function getLow()
    {
        return $this->low;
    }

    public function setLow($low): void
    {
        $this->low = $low;
    }

    public function getClose()
    {
        return $this->close;
    }

    public function setClose($close): void
    {
        $this->close = $close;
    }

    public function getVolume()
    {
        return $this->volume;
    }

    public function setVolume($volume): void
    {
        $this->volume = $volume;
    }

}

