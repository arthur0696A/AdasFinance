<?php
namespace AdasFinance\Controller;

use AdasFinance\Repository\AssetRepository;
use Exception;

class AssetController
{
    /** AssetRepository */
    private $assetRepository;

    public function __construct() 
    {
        $this->assetRepository = new AssetRepository();
    }

    public function searchBySymbolAction($parameters)
    {
        $result = $this->assetRepository->searchByQuery($parameters->symbol);
             
        echo json_encode($result);
        
    }
}
?>