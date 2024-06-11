<?php

namespace AdasFinance\AlphaVantage\Crypto;

class CryptoCurrencyDTO
{

    public $metaData;
    public $timeSeries;

    public function __construct($metaData, $timeSeries)
    {
        $this->metaData = $metaData;
        $this->timeSeries = $timeSeries;
    }

    public static function fromArray(array $data): CryptoCurrencyDTO
    {
        $metaData = $data['Meta Data'];
        $timeSeries = array_map(function ($date, $values) {
            return new TimeSeries(
                $date,
                $values['1. open'],
                $values['2. high'],
                $values['3. low'],
                $values['4. close'],
                $values['5. volume']
            );
        },
            array_keys($data['Time Series (Digital Currency Monthly)']),
            $data['Time Series (Digital Currency Monthly)']);

        if ($metaData && $timeSeries) {
            return new CryptoCurrencyDTO(
                new MetaData(
                    $metaData['1. Information'],
                    $metaData['2. Digital Currency Code'],
                    $metaData['3. Digital Currency Name'],
                    $metaData['4. Market Code'],
                    $metaData['5. Market Name'],
                    $metaData['6. Last Refreshed'],
                    $metaData['7. Time Zone']
                ),
                $timeSeries
            );
        }
        
        throw new \Exception('Daily rate limits exceded');
    }
}

?>
