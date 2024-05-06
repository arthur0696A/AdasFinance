<?php
namespace AdasFinance\Repository;

use AdasFinance\Entity\Asset;
use AdasFinance\Trait\RepositoryTrait;

class AssetRepository implements RepositoryInterface
{
    use RepositoryTrait;

    public function searchByQuery($symbol, $id)
    {
        $sql = "SELECT *
        FROM 
            Asset
        WHERE 
            id NOT IN (SELECT ua.asset_id FROM UserAsset ua WHERE ua.user_id = :userId)
            AND (symbol LIKE :symbol OR name LIKE :symbol)
        ORDER BY
            CASE
                WHEN symbol LIKE :symbol THEN 0
                ELSE 1
            END,
            symbol";
        
        $params = [
            ':symbol' => $symbol . '%',
            ':userId' => $id
        ];

        return $this->query($sql, $params);
    }

    public function update(Asset $asset)
    {
        $sql = "UPDATE
        Asset
        SET
            last_price = :last_price
        WHERE
            id = :id";
        
        $params = [
            ':id' => $asset->getId(),
            ':last_price' => $asset->getLastPrice(),
        ];

        $this->query($sql, $params);
    }
}