<?php

namespace AdasFinance\Controller;

use AdasFinance\Entity\Asset;
use AdasFinance\Repository\AssetRepository;
use AdasFinance\Repository\UserRepository;
use AdasFinance\AlphaVantage\AlphaVantageApiService;
use Exception;

class AssetController
{
    /** AssetRepository */
    private $assetRepository;

    /** UserRepository */
    private $userRepository;

    public function __construct()
    {
        $this->assetRepository = new AssetRepository();
        $this->userRepository = new UserRepository();
    }

    public function searchBySymbolAction($parameters)
    {
        $result = $this->assetRepository->searchByQuery($parameters->symbol, $_SESSION['user']->getId());

        echo json_encode($result);
    }

    public function chartHistoryAction($parameters)
    {
        $chartHistory = AlphaVantageApiService::getChartHistory($parameters->symbol);
        
        if ($chartHistory) {
            $asset = new Asset(null, $parameters->symbol, null, null, null, $chartHistory);
            $this->assetRepository->update($asset);
        } else {
            $chartHistory = $this->assetRepository->getChartHistoryBySymbol($parameters->symbol)['data'][0]->chart_history;
        }
        
        echo json_encode($chartHistory);
        
    }

}

?>