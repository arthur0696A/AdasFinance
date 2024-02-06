<?php
namespace AdasFinance\Repository;

use AdasFinance\Entity\UserAsset;
use AdasFinance\Trait\RepositoryTrait;

class UserAssetRepository implements RepositoryInterface
{
    use RepositoryTrait;

    public function getAssetsByUserId(int $id) 
    {
        $sql = "SELECT
            ua.id,
            ua.user_id,
            ua.asset_id,
            FORMAT(ua.average_price, 2) AS average_price,
            ua.percentage_goal,
            ua.quantity,
            a.symbol,
            a.name,
            FORMAT(a.last_price, 2) AS last_price
        FROM
            UserAsset ua
        JOIN Asset a on
            ua.asset_id = a.id
        WHERE
            ua.user_id = :id";
        
        $params = [
            ':id' => $id
        ];

        return $this->query($sql, $params);
    }

    public function getTotalAmountByUserId(int $id) 
    {
        $sql = "SELECT
            u.name,
            SUM(a.last_price * ua.quantity) as total_amount
        FROM
            UserAsset ua
        JOIN Asset a on
            ua.asset_id = a.id
        JOIN User u ON
            ua.user_id = u.id
        WHERE
            u.id = :id 
        GROUP BY
            u.id";
            
        $params = [
            ':id' => $id
        ];

        return $this->query($sql, $params);
    }

    public function setAssetGoalPercentage($parameters)
    {
        $sql = "UPDATE
            UserAsset
        SET
            percentage_goal = :value
        WHERE
            id = :id";
        
        $params = [
            ':value' => str_replace(',', '.', $parameters->newObjectivePercentageValue),
            ':id' => $parameters->userAssetId
        ];

        return $this->query($sql, $params);
    }

    public function insert(UserAsset $userAsset)
    {
        $sql = "INSERT
        INTO
            UserAsset(
            user_id,
            asset_id,
            average_price,
            quantity,
            percentage_goal
        )
        VALUES(
            :userId,
            :assetId,
            :averagePrice,
            :quantity,
            0.00
        )";
        
        $params = [
            ':userId' => $userAsset->getUserId(),
            ':assetId' => $userAsset->getAssetId(),
            ':averagePrice' => $userAsset->getAveragePrice(),
            ':quantity' => $userAsset->getQuantity(),
        ];

        return $this->query($sql, $params);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM UserAsset WHERE id = :id";
        
        $params = [
            ':id' => $id
        ];

        return $this->query($sql, $params);
    }

    public function getAssetByUserAndAssetId(UserAsset $userAsset)
    {
        $sql = "SELECT *
        FROM
            UserAsset ua
        JOIN Asset a ON
            ua.asset_id = a.id
        JOIN User u ON
            ua.user_id = u.id
        WHERE
            ua.asset_id = :assetId
            AND ua.user_id = :userId";
        
        $params = [
            ':assetId' => $userAsset->getAssetId(),
            ':userId' => $userAsset->getUserId(),
        ];

        return $this->query($sql, $params);
    }
}