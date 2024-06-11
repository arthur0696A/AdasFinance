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
        $setParts = [];
        $whereParts = [];
        $params = [];
        
        if ($asset->getId() !== null) {
            $whereParts[] = 'id = :id';
            $params[':id'] = $asset->getId();
        }

        if ($asset->getSymbol() !== null) {
            $whereParts[] = 'symbol = :symbol';
            $params[':symbol'] = $asset->getSymbol();
        }

        if ($asset->getLastPrice() !== null) {
            $setParts[] = 'last_price = :last_price';
            $params[':last_price'] = $asset->getLastPrice();
        }
    
        if ($asset->getChartHistory() !== null) {
            $setParts[] = 'chart_history = :chart_history';
            $params[':chart_history'] = $asset->getChartHistory();
        }
    
        if (empty($setParts)) {
            throw new \InvalidArgumentException('No fields to update');
        }

        if (empty($whereParts)) {
            throw new \InvalidArgumentException('No where clause');
        }

        $setClause = implode(', ', $setParts);
        $whereClause = implode(', ', $whereParts);

        $sql = "UPDATE Asset SET $setClause WHERE $whereClause";
    
        $this->query($sql, $params);

    }

    public function getChartHistoryBySymbol($symbol)
    {
        $sql = "SELECT chart_history
        FROM 
            Asset
        WHERE 
            symbol = :symbol";
        
        $params = [
            ':symbol' => $symbol,
        ];

        return $this->query($sql, $params);
    }
}