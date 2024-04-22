<?php
namespace AdasFinance\Entity;

use stdClass;

class UserAsset {

    private ?int $userAssetId;
    private ?int $userId;
    private ?int $assetId;
    private ?float $averagePrice;
    private ?float $quantity;

    public function __construct(
        ?int $userAssetId = null,
        ?int $userId = null,
        ?int $assetId = null,
        ?float $averagePrice = null,
        ?float $quantity = null
    ) {
        $this->userAssetId = $userAssetId;
        $this->userId = $userId;
        $this->assetId = $assetId;
        $this->averagePrice = $averagePrice;
        $this->quantity = $quantity;
    }

    public static function createFromParams(stdClass $params): self 
    {
        return new self(
            $params->id ?? self::convertToType($params->userAssetId, 'int'),
            self::convertToType($params->userId, 'int'),
            self::convertToType($params->assetId, 'int'),
            self::convertToType($params->averagePrice, 'float'),
            self::convertToType($params->quantity, 'float'),
        );
    }

    private static function convertToType($value, string $type) 
    {
        if (!is_null($value)) {
            settype($value, $type);
        }
       
        return $value;
    }

    public function getUserAssetId(): ?int
    {
        return $this->userAssetId;
    }

    public function setUserAssetId($userAssetId): self
    {
        $this->userAssetId = $userAssetId;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId($userId): self 
    {
        $this->userId = $userId;

        return $this;
    }

    public function getAssetId(): ?int
    {
        return $this->assetId;
    }

    public function setAssetId($assetId): self 
    {
        $this->assetId = $assetId;

        return $this;
    }

    public function getAveragePrice(): ?float
    {
        return $this->averagePrice;
    }

    public function setAveragePrice(float $averagePrice): self 
    {
        if ($averagePrice < 0) {
            throw new \InvalidArgumentException("Average price cannot be negative.");
        }

        $this->averagePrice = $averagePrice;
        return $this;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function setQuantity(float $quantity): self 
    {
        if ($quantity < 0) {
            throw new \InvalidArgumentException("Quantity cannot be negative.");
        }

        $this->quantity = $quantity;
        return $this;
    }
}

?>