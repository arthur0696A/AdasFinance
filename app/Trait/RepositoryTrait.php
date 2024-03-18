<?php
namespace AdasFinance\Trait;

use AdasFinance\Service\CamelCaseConverter;
use AdasFinance\Service\ConnectionCreator;
use AdasFinance\Entity\UserAsset;
use PDO;
use PDOException;
use stdClass;

trait RepositoryTrait {

    private PDO $pdo;

    public function query($sql, $params = [])
    {   
        try {
            $this->pdo = ConnectionCreator::getConnection();
            $stmt = $this->pdo->prepare($sql);
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

    private static function castToObject(stdClass $object)
    {
        $userAsset = CamelCaseConverter::convertToCamelCase($object);

        return UserAsset::createFromParams($userAsset);
    }
}

?>