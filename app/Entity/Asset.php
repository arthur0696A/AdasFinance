<?php
namespace AdasFinance\Entity;

use stdClass;

class Asset {
    
    private $id;
    private $symbol;
    private $name;
    private $lastPrice;
    private $groupType;
    private $chartHistory;

    public function __construct(
        ?int $id = null,
        ?string $symbol = null,
        ?string $name = null,
        ?float $lastPrice = null,
        ?int $groupType = null,
        ?string $chartHistory
    ) {
        $this->id = $id;
        $this->symbol = $symbol;
        $this->name = $name;
        $this->lastPrice = $lastPrice;
        $this->groupType = $groupType;
        $this->chartHistory = $chartHistory;
    }

    public static function createFromParams(stdClass $params): self 
    {
        return new self(
            self::convertToType($params->id, 'int'),
            self::convertToType($params->symbol, 'string'),
            self::convertToType($params->name, 'string'),
            self::convertToType($params->lastPrice, 'double'),
            self::convertToType($params->groupType, 'int'),
            self::convertToType($params->chartHistory, 'string'),
        );
    }

    private static function convertToType($value, string $type) 
    {
        if (!is_null($value)) {
            settype($value, $type);
        }
       
        return $value;
    }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of symbol
     */ 
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * Set the value of symbol
     *
     * @return  self
     */ 
    public function setSymbol($symbol)
    {
        $this->symbol = $symbol;

        return $this;
    }

    /**
     * Get the value of name
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */ 
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of lastPrice
     */ 
    public function getLastPrice()
    {
        return $this->lastPrice;
    }

    /**
     * Set the value of lastPrice
     *
     * @return  self
     */ 
    public function setLastPrice($lastPrice)
    {
        $this->lastPrice = $lastPrice;

        return $this;
    }

    /**
     * Get the value of groupType
     */ 
    public function getGroupType()
    {
        return $this->groupType;
    }

    /**
     * Set the value of groupType
     *
     * @return  self
     */ 
    public function setGroupType($groupType)
    {
        $this->groupType = $groupType;

        return $this;
    }


    /**
     * Get the value of chartHistory
     */ 
    public function getChartHistory()
    {
        return $this->chartHistory;
    }

    /**
     * Set the value of chartHistory
     *
     * @return  self
     */ 
    public function setChartHistory($chartHistory)
    {
        $this->chartHistory = $chartHistory;

        return $this;
    }

}
