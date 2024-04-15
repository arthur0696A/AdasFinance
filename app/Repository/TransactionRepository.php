<?php
namespace AdasFinance\Repository;

use AdasFinance\Entity\Transaction;
use AdasFinance\Trait\RepositoryTrait;

class TransactionRepository implements RepositoryInterface
{
    use RepositoryTrait;

    public function insert(Transaction $transaction)
    {
        $sql = "INSERT
        INTO
            Transaction(
            user_id,
            asset_id,
            transaction_type_id,
            transaction_date,
            price,
            quantity
        )
        VALUES(
            :userId,
            :assetId,
            :transactionTypeId,
            :transactionDate,
            :price,
            :quantity
        )";
        
        $params = [
            ':userId' => $transaction->getUserId(),
            ':assetId' => $transaction->getAssetId(),
            ':transactionTypeId' => $transaction->getTransactionTypeId(),
            ':transactionDate' => $transaction->getTransactionDate(),
            ':price' => $transaction->getPrice(),
            ':quantity' => $transaction->getQuantity(),
        ];

        return $this->query($sql, $params);
    }

    public function getAllTransactionsByUserAssetId($userId, $assetId)
    {
        $sql = "SELECT * 
        FROM Transaction 
        WHERE user_id = :userId 
        AND asset_id = :assetId";
        
        $params = [
            ':userId' => $userId,
            ':assetId' => $assetId
        ];

        $result = $this->query($sql, $params);
        $transactions = [];

        foreach($result['data'] as $transaction) {
            $transactions[] = self::castToObject($transaction, 'Transaction');
        }
        
        return $transactions;
    }
}