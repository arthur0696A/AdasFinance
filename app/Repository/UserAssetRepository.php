<?php
namespace AdasFinance\Repository;

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
}