<?php
namespace AdasFinance\Controller;

use AdasFinance\Entity\Transaction;
use AdasFinance\Entity\UserAsset;
use AdasFinance\Repository\UserAssetRepository;
use AdasFinance\Repository\TransactionRepository;
use AdasFinance\Service\AssetTransactionManager;
use AdasFinance\Service\CamelCaseConverter;
use Exception;

class UserAssetController
{
    /** UserAssetRepository */
    private $userAssetRepository;

    /** TransactionRepository */
    private $transactionRepository;

    /** AssetTransactionManager */
    private $assetTransactionManager;
    
    public function __construct() 
    {
        $this->userAssetRepository = new UserAssetRepository();
        $this->transactionRepository = new TransactionRepository();
        $this->assetTransactionManager = new AssetTransactionManager($this->userAssetRepository);
    }

    public function homeAction()
    {
        try {
            $userId = $_SESSION['user']->getId();
            $userAssets = $this->userAssetRepository->getAssetsByUserId($userId);
            $userAssets = $this->handleResult($userAssets);
    
            $totalUserAmount = $this->userAssetRepository->getTotalAmountByUserId($userId);
            $totalUserAmount = !empty($totalUserAmount['data'])  ? $this->handleResult($totalUserAmount)[0]['total_amount'] : null;
            $groupTotal = [];

            include '../view/home.html';

            foreach ($userAssets as &$userAsset) {
                $transactions = $this->transactionRepository->getAllTransactionsByUserAssetId($userAsset['user_id'], $userAsset['asset_id']);

                $lastPrice = str_replace(",", "", $userAsset['last_price']);
                $averagePrice = str_replace(",", "", $userAsset['average_price']);

                $userAsset['total_value'] = $this->numberFormat($userAsset['quantity'] * $lastPrice);
                $userAsset['asset_price_difference'] = $this->numberFormat(($lastPrice - $averagePrice) / $averagePrice * 100);
                $userAsset['total_price_difference'] = $this->numberFormat(($lastPrice - $averagePrice) * $userAsset['quantity']);
                $userAsset['percentage'] = $this->numberFormat($userAsset['quantity'] * $lastPrice * 100 / $totalUserAmount);
                $userAsset['percentage_goal'] = $this->numberFormat($userAsset['percentage_goal']);
                $userAsset['average_price'] = $this->numberFormat($averagePrice);
                $userAsset['last_price'] = $this->numberFormat($lastPrice);
                
                if (!isset($groupTotal[$userAsset['group_type']])) {
                    $groupTotal[$userAsset['group_type']] = 0;
                }
                
                $groupTotal[$userAsset['group_type']] += $userAsset['quantity'] * $lastPrice * 100 / $totalUserAmount;

                include '../view/asset.html';
            }

            foreach ($groupTotal as &$total) {
                $total = $this->numberFormat($total);
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

        $result = $this->assetTransactionManager->create($userAsset);
        
        echo json_encode(['success' => true, 'message' => 'Dados cadastrados com sucesso']);

        header('Location: home');
        exit;
    }

    public function registerTransactionAction($parameters)
    {
        $parameters = CamelCaseConverter::convertToCamelCase($parameters);
        $parameters->userId = $_SESSION['user']->getId();
        $transaction = Transaction::createFromParams($parameters);

        $result = $this->assetTransactionManager->registerTransaction($transaction);
        
        echo json_encode(['success' => true, 'message' => 'Dados cadastrados com sucesso']);

        header('Location: home');
        exit;
    }

    public function userAssetDeleteAction($parameters)
    {
        $result = $this->userAssetRepository->delete($parameters->userAssetId);

        return json_encode(['success' => true, 'message' => 'Ativo excluido com sucesso']);
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