<?php
namespace AdasFinance\Repository;

use AdasFinance\Entity\Asset;
use AdasFinance\Trait\RepositoryTrait;

class AssetRepository implements RepositoryInterface
{
    use RepositoryTrait;

    public function searchByQuery($query)
    {
        $sql = "SELECT *
        FROM 
            Asset
        WHERE 
            symbol LIKE :query
        OR 
            name LIKE :query
        ORDER BY
            CASE
                WHEN symbol LIKE :query THEN 0
                ELSE 1
            END,
            symbol";
        
        $params = [
            ':query' => $query . '%'
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