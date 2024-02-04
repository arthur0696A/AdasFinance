<?php
namespace AdasFinance\Service;

use AdasFinance\Entity\UserAsset;

class AssetTransactionManager
{
    /** UserAssetRepository */
    private $userAssetRepository;

    public function __construct($userAssetRepository) 
    {
        $this->userAssetRepository = $userAssetRepository;
    }

    public function buy(UserAsset $userAsset)
    {
        $userDoesntHaveAsset = empty($this->userAssetRepository->getAssetByUserAndAssetId($userAsset)['data']);
        if ($userDoesntHaveAsset) {
            $this->userAssetRepository->insert($userAsset);
            return;
        }

        $this->userAssetRepository->update($userAsset);
    }
}