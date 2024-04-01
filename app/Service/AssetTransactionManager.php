<?php
namespace AdasFinance\Service;

use AdasFinance\Entity\UserAsset;
use AdasFinance\Entity\Transaction;
use AdasFinance\Repository\TransactionRepository;
use AdasFinance\Repository\UserRepository;

class AssetTransactionManager
{
    /** UserRepository */
    private $userRepository;

    /** UserAssetRepository */
    private $userAssetRepository;

    /** TransactionRepository */
    private $transactionRepository;

    public function __construct($userAssetRepository) 
    {
        $this->userRepository = new UserRepository();
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
        if ($transaction->getTransactionTypeId() == 1) {
            return $this->buy($userAsset, $transaction);
        } else {
            return $this->sell($userAsset, $transaction);
        }
    }

    private function buy(UserAsset $userAsset, Transaction $transaction): UserAsset
    {
        $userAssetQuantity = $userAsset->getQuantity();
        $transactionQuantity = $transaction->getQuantity();
        $totalQuantity = $userAssetQuantity + $transactionQuantity;

        $userAssetAveragePrice = $userAsset->getAveragePrice();
        $transactionAveragePrice = $transaction->getPrice();

        $newAveragePrice = (($userAssetQuantity * $userAssetAveragePrice) + ($transactionQuantity * $transactionAveragePrice)) / $totalQuantity;

        $userAsset->setQuantity($totalQuantity);
        $userAsset->setAveragePrice($newAveragePrice);

        return $userAsset;
    }

    private function sell(UserAsset $userAsset, Transaction $transaction): UserAsset
    {
        $totalQuantity = $userAsset->getQuantity() - $transaction->getQuantity();
        $userAsset->setQuantity($totalQuantity);
        
        $user = $this->userRepository->getById($userAsset->getUserId());
        $totalBalance = $user->getTotalBalance();
        $newTotalBalance = $totalBalance + ($transaction->getPrice() - $userAsset->getAveragePrice()) * $transaction->getQuantity();
        $user->setTotalBalance($newTotalBalance);

        $this->userRepository->update($user);
        $_SESSION['user'] = $user;
        
        return $userAsset;
    }
    
}