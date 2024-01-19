<?php
namespace AdasFinance\Repository;

use AdasFinance\Service\ConnectionCreator;
use PDO;
use PDOException;

class UserRepository
{
    private function query($sql, $params = [])
    {   
        try {
            $connection = ConnectionCreator::getConnection();
            $stmt = $connection->prepare($sql);
            $stmt->execute($params);

            $results = $stmt->fetchAll(PDO::FETCH_CLASS);
            return [
                'status' => 'success',
                'data' => $results
            ];

        } catch (PDOException $err) {
            return [
                'status' => 'error',
                'data' => $err->getMessage()
            ];
        }
    }

    public function getUserByUsername(string $username) 
    {
        $sql = "SELECT * FROM User WHERE username = :user OR email = :user"; 
        $params = [
            ':user' => $username
        ];

        return $this->query($sql, $params);
    }

    public function saveUser(array $parameters)
    {
        $sql = "INSERT INTO User (name, email, username, password) VALUES (:name, :email, :username, :password)"; 
        $params = [
            ':name' => $parameters['name'],
            ':email' => $parameters['email'],
            ':username' => $parameters['username'],
            ':password' => password_hash($parameters['password'], PASSWORD_DEFAULT),
        ];

        return $this->query($sql, $params);
    }
}

?>
