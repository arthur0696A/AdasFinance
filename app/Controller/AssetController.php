<?php
namespace AdasFinance\Controller;

use AdasFinance\Repository\AssetRepository;
use AdasFinance\Repository\UserAssetRepository;
use AdasFinance\Service\AlphaVantageApiService;
use Exception;

class AssetController
{
    /** AssetRepository */
    private $assetRepository;

    /** UserAssetRepository */
    private $userAssetRepository;
    
    public function __construct() 
    {
        $this->assetRepository = new AssetRepository();
        $this->userAssetRepository = new UserAssetRepository();
    }

    public function homeAction()
    {
        include '../view/home.html';
        try {
            $userId = $_SESSION['user']->getId();
            $assets = $this->userAssetRepository->getAssetsByUserId($userId);
            $assets = $this->handleResult($assets);
    
            if (empty($assets)) {
                throw new Exception("User does not have any registered assets");
            }
    
            $totalUserAmount = $this->userAssetRepository->getTotalAmountByUserId($userId);
            $totalUserAmount = $this->handleResult($totalUserAmount)[0]['total_amount'];
    
            foreach ($assets as &$asset) {
                $asset['total_value'] = $this->numberFormat($asset['quantity'] * $asset['last_price']);
                $asset['asset_price_difference'] = $this->numberFormat(($asset['last_price'] - $asset['average_price']) / $asset['average_price'] * 100);
                $asset['total_price_difference'] = $this->numberFormat($asset['last_price'] * 100 - $asset['average_price'] * 100);
                $asset['percentage'] = $this->numberFormat(($asset['last_price'] * 100 / $totalUserAmount) * 100);
                $asset['percentage_goal'] = $this->numberFormat($asset['percentage_goal']);
                $asset['average_price'] = $this->numberFormat($asset['average_price']);
                include '../view/asset.html';
            }
        } catch(Exception $exception) {
            
        }

        include '../view/home-end.html';
    }

    public function assetGoalPercentageAction($parameters)
    {
        $result = $this->userAssetRepository->setAssetGoalPercentage($parameters);

        return json_encode(['success' => true, 'message' => 'Dados cadastrados com sucesso']);
    }

    public function assetSaveAction($parameters)
    {
        if($this->assetRepository->getAll()) {
        //ja existe
        }


        $result = $this->assetRepository->saveNewAssset($parameters);
        //trigga a buyaction

        return json_encode(['success' => true, 'message' => 'Dados cadastrados com sucesso']);
    }

    public function buyAssetAction($parameters)
    {
        $result = $this->userAssetRepository->saveAssset($parameters);
        //trigga a buyaction

        return json_encode(['success' => true, 'message' => 'Dados cadastrados com sucesso']);
    }

    public function sellAssetAction($parameters)
    {
        $result = $this->userAssetRepository->saveAssset($parameters);
        //trigga a buyaction

        return json_encode(['success' => true, 'message' => 'Dados cadastrados com sucesso']);
    }

    public function searchBySymbolAction($parameters)
    {
        $result = $this->assetRepository->searchByQuery($parameters->symbol);
             
        echo json_encode($result);
        
    }

    private function handleResult(array $result)
    {
        $finalData = [];
        if (isset($result['data']) && is_array($result['data'])) {
            foreach ($result['data'] as $data) {
                $dataArray = json_decode(json_encode($data), true);
                $finalData[] = $dataArray;
            }
        }

        return $finalData;
    }

    private function numberFormat($value)
    {
        return number_format($value, 2, ',', '.');
    }
}
?>