<?php
namespace AdasFinance\Entity;

class UserAsset {

    private $userAssetId;
    private $userId;
    private $assetId;
    private $averagePrice;
    private $quantity;

    public function __construct($userAssetId, $userId, $assetId, $averagePrice, $quantity) 
    {
        $this->userAssetId = $userAssetId;
        $this->userId = $userId;
        $this->assetId = $assetId;
        $this->averagePrice = $averagePrice;
        $this->quantity = $quantity;
    }

}

?>