<?php
namespace AdasFinance\AlphaVantage;

class GlobalQuoteDTO {
    private $symbol;
    private $open;
    private $high;
    private $low;
    private $price;
    private $volume;
    private $latestTradingDay;
    private $previousClose;
    private $change;
    private $changePercent;

    public function __construct(
        string $symbol,
        string $open,
        string $high,
        string $low,
        string $price,
        string $volume,
        string $latestTradingDay,
        string $previousClose,
        string $change,
        string $changePercent
    ) {
        $this->symbol = $symbol;
        $this->open = $open;
        $this->high = $high;
        $this->low = $low;
        $this->price = $price;
        $this->volume = $volume;
        $this->latestTradingDay = $latestTradingDay;
        $this->previousClose = $previousClose;
        $this->change = $change;
        $this->changePercent = $changePercent;
    }

    public static function fromArray(array $data): GlobalQuoteDTO 
    {
        $globalQuoteData = $data['Global Quote'];

        return new GlobalQuoteDTO(
            $globalQuoteData['01. symbol'],
            $globalQuoteData['02. open'],
            $globalQuoteData['03. high'],
            $globalQuoteData['04. low'],
            $globalQuoteData['05. price'],
            $globalQuoteData['06. volume'],
            $globalQuoteData['07. latest trading day'],
            $globalQuoteData['08. previous close'],
            $globalQuoteData['09. change'],
            $globalQuoteData['10. change percent']
        );
    }

    public function getSymbol(): string {
        return $this->symbol;
    }

    public function setSymbol(string $symbol): void {
        $this->symbol = $symbol;
    }

    public function getOpen(): string {
        return $this->open;
    }

    public function setOpen(string $open): void {
        $this->open = $open;
    }

    public function getHigh(): string {
        return $this->high;
    }

    public function setHigh(string $high): void {
        $this->high = $high;
    }

    public function getLow(): string {
        return $this->low;
    }

    public function setLow(string $low): void {
        $this->low = $low;
    }

    public function getPrice(): string {
        return $this->price;
    }

    public function setPrice(string $price): void {
        $this->price = $price;
    }

    public function getVolume(): string {
        return $this->volume;
    }

    public function setVolume(string $volume): void {
        $this->volume = $volume;
    }

    public function getLatestTradingDay(): string {
        return $this->latestTradingDay;
    }

    public function setLatestTradingDay(string $latestTradingDay): void {
        $this->latestTradingDay = $latestTradingDay;
    }

    public function getPreviousClose(): string {
        return $this->previousClose;
    }

    public function setPreviousClose(string $previousClose): void {
        $this->previousClose = $previousClose;
    }

    public function getChange(): string {
        return $this->change;
    }

    public function setChange(string $change): void {
        $this->change = $change;
    }

    public function getChangePercent(): string {
        return $this->changePercent;
    }

    public function setChangePercent(string $changePercent): void {
        $this->changePercent = $changePercent;
    }
}

?>
