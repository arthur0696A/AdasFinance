<?php
namespace AdasFinance\Entity;

use stdClass;

class Transaction {

    private int $userId;
    private int $assetId;
    private int $transactionTypeId;
    private string $transactionDate;
    private float $price;
    private int $quantity;

    public function __construct(
        int $userId,
        int $assetId,
        int $transactionTypeId,
        string $transactionDate,
        float $price,
        int $quantity,
    ) {
        $this->userId = $userId;
        $this->assetId = $assetId;
        $this->transactionTypeId = $transactionTypeId;
        $this->transactionDate = $transactionDate;
        $this->price = $price;
        $this->quantity = $quantity;
    }

    public static function createFromParams(stdClass $params): self 
    {
        return new self(
            self::convertToType($params->userId, 'int'),
            self::convertToType($params->assetId, 'int'),
            self::convertToType($params->transactionTypeId, 'int'),
            self::convertToType($params->transactionDate, 'date'),
            self::convertToType($params->price, 'float'),
            self::convertToType($params->quantity, 'int'),
        );
    }

    private static function convertToType($value, string $type)
    {
        switch ($type) {
            case 'int':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'date':
                return (string) $value;
            default:
                return $value;
        }
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

    public function getTransactionTypeId(): ?int
    {
        return $this->transactionTypeId;
    }

    public function setTransactionTypeId($transactionTypeId): self 
    {
        $this->transactionTypeId = $transactionTypeId;

        return $this;
    }

    public function getTransactionDate(): ?string
    {
        return $this->transactionDate;
    }

    public function setTransactionDate($transactionDate): self 
    {
        $this->transactionDate = $transactionDate;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self 
    {
        if ($price < 0) {
            throw new \InvalidArgumentException("Price cannot be negative.");
        }

        $this->price = $price;
        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self 
    {
        if ($quantity < 0) {
            throw new \InvalidArgumentException("Quantity cannot be negative.");
        }

        $this->quantity = $quantity;
        return $this;
    }
}

?>