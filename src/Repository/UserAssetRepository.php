<?php
namespace AdasFinance\Repository;

use AdasFinance\Service\ConnectionCreator;
use AdasFinance\Service\RepositoryTrait;

class UserAssetRepository implements RepositoryInterface
{
    use RepositoryTrait;

    public function teste() 
    {
        $sql = "SELECT * FROM UserAsset"; 
        $params = [
            
        ];

        return $this->query($sql, $params);
    }
}