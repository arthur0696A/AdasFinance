<?php
namespace AdasFinance\Repository;

use AdasFinance\Entity\User;
use AdasFinance\Trait\RepositoryTrait;

class UserRepository implements RepositoryInterface
{
    use RepositoryTrait;

    public function getById(int $id)
    {
        $sql = "SELECT * FROM User WHERE id = :id"; 
        $params = [
            ':id' => $id
        ];

        $result = $this->query($sql, $params);

        return self::castToObject($result['data'][0], 'User');

    }

    public function update(User $user)
    {
        $sql = "UPDATE
        User
        SET
            total_balance = :total_balance
        WHERE
            id = :id";
        
        $params = [
            ':id' => $user->getId(),
            ':total_balance' => $user->getTotalBalance(),
        ];

        $this->query($sql, $params);
    }

    public function getUserByUsername(string $username) 
    {
        $sql = "SELECT * FROM User WHERE username = :user OR email = :user"; 
        $params = [
            ':user' => $username
        ];

        $result = $this->query($sql, $params);

        return self::castToObject($result['data'][0], 'User');

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
