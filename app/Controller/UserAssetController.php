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
            $totalUserAmount = !empty($totalUserAmount['data']) ? $this->handleResult(
                $totalUserAmount
            )[0]['total_amount'] : null;
            $groupTotal = [];

            include '../view/home.html';

            foreach ($userAssets as &$userAsset) {
                $transactions = $this->transactionRepository->getAllTransactionsByUserAssetId(
                    $userAsset['user_id'],
                    $userAsset['asset_id']
                );

                $lastPrice = str_replace(",", "", $userAsset['last_price']);
                $averagePrice = str_replace(",", "", $userAsset['average_price']);

                $userAsset['total_value'] = $this->numberFormat($userAsset['quantity'] * $lastPrice);
                $userAsset['asset_price_difference'] = $this->numberFormat(
                    ($lastPrice - $averagePrice) / $averagePrice * 100
                );
                $userAsset['total_price_difference'] = $this->numberFormat(
                    ($lastPrice - $averagePrice) * $userAsset['quantity']
                );
                $userAsset['percentage'] = $this->numberFormat(
                    $userAsset['quantity'] * $lastPrice * 100 / $totalUserAmount
                );
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
        } catch (Exception $exception) {
            $_SESSION['error'] = $exception->getMessage();
        } finally {
            include '../view/home-end.html';
        }
    }

    public function userAssetGoalPercentageAction($parameters)
    {
        try {
            $this->userAssetRepository->setAssetGoalPercentage($parameters);

            return json_encode(['success' => true, 'message' => 'Dados cadastrados com sucesso']);
        } catch (Exception $exception) {
            return json_encode(['success' => false, 'message' => 'Erro ao cadastrar dados']);
        }
    }

    public function userAssetSaveAction(
        $parameters
    ) {
        try {
            $parameters = CamelCaseConverter::convertToCamelCase($parameters);
            $parameters->userId = $_SESSION['user']->getId();
            $userAsset = UserAsset::createFromParams($parameters);

            $this->assetTransactionManager->create($userAsset);
        } catch (Exception $exception) {
            $_SESSION['error'] = 'Erro ao cadastrar ativo';
        } finally {
            header('Location: home');
            exit;
        }
    }

    public
    function registerTransactionAction(
        $parameters
    ) {
        try {
            $parameters = CamelCaseConverter::convertToCamelCase($parameters);
            $parameters->userId = $_SESSION['user']->getId();
            $transaction = Transaction::createFromParams($parameters);

            $this->assetTransactionManager->registerTransaction($transaction);
        } catch (Exception $exception) {
            $_SESSION['error'] = 'Erro ao registrar transação';
        } finally {
            header('Location: home');
            exit;
        }
    }

    public
    function userAssetDeleteAction(
        $parameters
    ) {
        try {
            $this->userAssetRepository->delete($parameters->userAssetId);

            return json_encode(['success' => true, 'message' => 'Ativo excluido com sucesso']);
        } catch (Exception $exception) {
            return json_encode(['success' => false, 'message' => 'Erro ao excluir ativo']);
        }
    }

    private
    function handleResult(
        array $result
    ) {
        $finalData = [];
        if (isset($result['data']) && is_array($result['data'])) {
            foreach ($result['data'] as $data) {
                $dataArray = json_decode(json_encode($data), true);
                $finalData[] = $dataArray;
            }
        }

        return $finalData;
    }

    private
    function numberFormat(
        $value
    ) {
        return number_format($value, 2, ',', '.');
    }
}