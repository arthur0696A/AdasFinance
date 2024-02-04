<?php
namespace AdasFinance\Controller;

use AdasFinance\Entity\UserAsset;
use AdasFinance\Repository\UserAssetRepository;
use AdasFinance\Service\AssetTransactionManager;
use AdasFinance\Service\CamelCaseConverter;
use Exception;

class UserAssetController
{
    /** UserAssetRepository */
    private $userAssetRepository;

    /** AssetTransactionManager */
    private $assetTransactionManager;
    
    public function __construct() 
    {
        $this->userAssetRepository = new UserAssetRepository();
        $this->assetTransactionManager = new AssetTransactionManager($this->userAssetRepository);
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

    public function userAssetGoalPercentageAction($parameters)
    {
        $result = $this->userAssetRepository->setAssetGoalPercentage($parameters);

        return json_encode(['success' => true, 'message' => 'Dados cadastrados com sucesso']);
    }

    public function userAssetSaveAction($parameters)
    {
        $parameters = CamelCaseConverter::convertToCamelCase($parameters);
        $parameters->userId = $_SESSION['user']->getId();
        $userAsset = UserAsset::createFromParams($parameters);

        $result = $this->assetTransactionManager->buy($userAsset);
        
        echo json_encode(['success' => true, 'message' => 'Dados cadastrados com sucesso']);

        header('Location: home');
        exit;
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