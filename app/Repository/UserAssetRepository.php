<?php
namespace AdasFinance\Repository;

use AdasFinance\Entity\UserAsset;
use AdasFinance\Service\CamelCaseConverter;
use AdasFinance\Trait\RepositoryTrait;
use stdClass;

class UserAssetRepository implements RepositoryInterface
{
    use RepositoryTrait;

    public function getById(int $id)
    {
        $sql = "SELECT *
        FROM
            UserAsset ua
        WHERE
            ua.asset_id = :id";
        
        $params = [
            ':id' => $id
        ];

        $result = $this->query($sql, $params);
        
        return UserAsset::createFromParams($result['data']);
    }

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


        $this->query($sql, $params);

        $lastInsertId = $this->pdo->lastInsertId();
    
        $query = "SELECT * FROM UserAsset WHERE id = :lastInsertId";
        $result = $this->query($query, [':lastInsertId' => $lastInsertId]);
    
        return self::castToObject($result['data'][0], 'UserAsset');

    }

    public function update(UserAsset $userAsset)
    {
        $sql = "UPDATE
        UserAsset
        SET
            average_price = :average_price,
            quantity = :quantity
        WHERE
            id = :id";
        
        $params = [
            ':id' => $userAsset->getUserAssetId(),
            ':average_price' => $userAsset->getAveragePrice(),
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

    public function getAssetByUserAndAssetId(int $userId, int $assetId): UserAsset
    {
        $sql = "SELECT ua.*
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
            ':assetId' => $assetId,
            ':userId' => $userId,
        ];

        $result = $this->query($sql, $params);

        return self::castToObject($result['data'][0], 'UserAsset');
    }

}