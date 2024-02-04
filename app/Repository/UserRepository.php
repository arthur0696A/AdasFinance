<?php
namespace AdasFinance\Repository;

use AdasFinance\Trait\RepositoryTrait;

class UserRepository implements RepositoryInterface
{
    use RepositoryTrait;

    public function getUserByUsername(string $username) 
    {
        $sql = "SELECT * FROM User WHERE username = :user OR email = :user"; 
        $params = [
            ':user' => $username
        ];

        return $this->query($sql, $params);
    }

    public function saveUser($parameters)
    {
        $sql = "INSERT INTO User (name, email, username, password) VALUES (:name, :email, :username, :password)"; 
        $params = [
            ':name' => $parameters->name,
            ':email' => $parameters->email,
            ':username' => $parameters->username,
            ':password' => password_hash($parameters->password, PASSWORD_DEFAULT),
        ];

        return $this->query($sql, $params);
    }
}

?>
