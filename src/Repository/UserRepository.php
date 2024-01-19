<?php
namespace AdasFinance\Repository;

use AdasFinance\Entity\User;
use AdasFinance\Service\ConnectionCreator;
use PDO;

class UserRepository
{
    public function getUser(string $username) 
    {
        $connection = ConnectionCreator::getConnection();
        $params = [
            ':user' => $username
        ];

        $sql = "SELECT * FROM User WHERE username = :user OR email = :user";
        $stmt = $connection->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_CLASS);
        $user = null;
        
        if(count($results) > 0) {
            $user = new User($results[0]);
        }

        return $user;
    }

    public function listAll() 
    {
        $sql = "SELECT * FROM User";
        $connection = ConnectionCreator::getConnection();
        $result = $connection->query($sql);

        return $result;
    }
}

?>
