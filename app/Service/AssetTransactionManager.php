<?php
namespace AdasFinance\Service;

use AdasFinance\Entity\UserAsset;
use AdasFinance\Entity\Transaction;
use AdasFinance\Repository\TransactionRepository;

class AssetTransactionManager
{
    private const BUY = 'BUY';
    private const SELL = 'SELL';

    /** UserAssetRepository */
    private $userAssetRepository;

    /** TransactionRepository */
    private $transactionRepository;

    public function __construct($userAssetRepository) 
    {
        $this->userAssetRepository = $userAssetRepository;
        $this->transactionRepository = new TransactionRepository();
    }

    public function create(UserAsset $userAsset)
    {
        $userAsset = $this->userAssetRepository->insert($userAsset);
        
        $transaction = new Transaction(
            $userAsset->getUserId(),
            $userAsset->getAssetId(),
            1,
            date("Y-m-d h:i:s"),
            $userAsset->getAveragePrice(),
            $userAsset->getQuantity(),
        );

        $this->transactionRepository->insert($transaction);
    }

    public function registerTransaction(Transaction $transaction)
    {
        $userAsset = $this->userAssetRepository->getAssetByUserAndAssetId($transaction->getUserId(), $transaction->getAssetId());

        $userAsset = $this->updateUserAssetValues($userAsset, $transaction);
        $this->userAssetRepository->update($userAsset);
        $this->transactionRepository->insert($transaction);
    }

    private function updateUserAssetValues(UserAsset $userAsset, Transaction $transaction): UserAsset
    {
        // FAZER ----- PAREI AQUI
        return $userAsset;
    }
}