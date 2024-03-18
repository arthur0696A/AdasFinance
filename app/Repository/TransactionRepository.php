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
}