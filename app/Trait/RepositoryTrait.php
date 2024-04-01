<?php
namespace AdasFinance\Trait;

use AdasFinance\Service\CamelCaseConverter;
use AdasFinance\Service\ConnectionCreator;
use AdasFinance\Entity\User;
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

    private static function castToObject(?stdClass $object = null, String $class)
    {
        if(!$object) {
            return null;
        }

        $formattedObject = CamelCaseConverter::convertToCamelCase($object);

        switch($class) {
            case 'UserAsset':
                return UserAsset::createFromParams($formattedObject);
                break;
            case 'User':
                return User::createFromParams($formattedObject);
                break;
        }
    }
}

?>