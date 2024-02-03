<?php
namespace AdasFinance\Repository;

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
}