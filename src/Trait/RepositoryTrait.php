<?php
namespace AdasFinance\Trait;

use AdasFinance\Service\ConnectionCreator;
use PDO;
use PDOException;

trait RepositoryTrait {

    public function query($sql, $params = [])
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
}

?>