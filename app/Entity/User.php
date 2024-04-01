<?php
namespace AdasFinance\Entity;

use stdClass;

class User {
    
    private $id;
    private $username;
    private $name;
    private $password;
    private $email;
    private $totalBalance;

    public function __construct(
        ?int $id = null,
        ?string $username = null,
        ?string $name = null,
        ?string $password = null,
        ?string $email = null,
        ?float $totalBalance = null
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->name = $name;
        $this->password = $password;
        $this->email = $email;
        $this->totalBalance = $totalBalance;
    }

    public static function createFromParams(stdClass $params): self 
    {
        return new self(
            self::convertToType($params->id, 'int'),
            self::convertToType($params->username, 'string'),
            self::convertToType($params->name, 'string'),
            self::convertToType($params->password, 'string'),
            self::convertToType($params->email, 'string'),
            self::convertToType($params->totalBalance, 'double')
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
     * Get the value of username
     */ 
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set the value of username
     *
     * @return  self
     */ 
    public function setUsername($username)
    {
        $this->username = $username;

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
     * Get the value of password
     */ 
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @return  self
     */ 
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the value of email
     */ 
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */ 
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of total balance
     */ 
    public function getTotalBalance()
    {
        return $this->totalBalance;
    }

    /**
     * Set the value of total balance
     *
     * @return  self
     */ 
    public function setTotalBalance($totalBalance)
    {
        $this->totalBalance = $totalBalance;

        return $this;
    }
}

?>