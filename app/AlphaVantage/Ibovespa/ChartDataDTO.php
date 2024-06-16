<?php

namespace AdasFinance\AlphaVantage\Ibovespa;

class ChartDataDTO
{
    private $metaData;
    private $timeSeries;

    public function __construct(MetaDataDTO $metaData, array $timeSeries)
    {
        $this->metaData = $metaData;
        $this->timeSeries = $timeSeries;
    }

    public static function convertToDTOs($json) {
    
        if ($json->{'Meta Data'}) {
            $metaData = new MetaDataDTO(
                $json->{'Meta Data'}->{'1. Information'},
                $json->{'Meta Data'}->{'2. Symbol'},
                $json->{'Meta Data'}->{'3. Last Refreshed'},
                $json->{'Meta Data'}->{'4. Time Zone'}
            );
        }
        
        $timeSeries = [];
        if ($json->{'Monthly Time Series'}) {
            foreach ($json->{'Monthly Time Series'} as $date => $series) {
                $timeSeries[] = new TimeSeriesDTO(
                    $date,
                    $series->{'1. open'},
                    $series->{'2. high'},
                    $series->{'3. low'},
                    $series->{'4. close'},
                    $series->{'5. volume'}
                );
            }
        }
    
        if (empty($timeSeries)) {
            return null;
        }

        return new ChartDataDTO($metaData, $timeSeries);
    }

    public function getMetaData(): MetaDataDTO
    {
        return $this->metaData;
    }

    public function setMetaData(MetaDataDTO $metaData): void
    {
        $this->metaData = $metaData;
    }

    public function getTimeSeries(): array
    {
        return $this->timeSeries;
    }

    public function setTimeSeries(array $timeSeries): void
    {
        $this->timeSeries = $timeSeries;
    }
}

