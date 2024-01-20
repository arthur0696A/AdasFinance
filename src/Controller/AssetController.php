<?php
namespace AdasFinance\Controller;

use AdasFinance\Repository\UserAssetRepository;

class AssetController
{
    /** UserAssetRepository */
    private $userAssetRepository;
    
    public function __construct() 
    {
        $this->userAssetRepository = new UserAssetRepository();
    }

    public function homeAction()
    {
        $result = $this->userAssetRepository->teste();
        $assets = [];
        
        if (isset($result['data']) && is_array($result['data'])) {
            foreach ($result['data'] as $asset) {
                $assetArray = json_decode(json_encode($asset), true);
                $assets[] = $assetArray;
            }
        }

        include '../view/home.html';
    }
}
?>