<?php
namespace AdasFinance\Service;

require_once '../public/config.php';
use PDO;

class ConnectionCreator
{
    public static $connection;

    public static function getConnection() 
    {
        if (!isset(self::$connection)) {
            self::$connection = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return self::$connection;
    }

    // public function query($sql, $params = [])
    // {
    //     try {
    //         $stmt = $this->pdo->prepare($sql);
    //         $stmt->execute($params);

    //         $results = $stmt->fetchAll(PDO::FETCH_CLASS);
    //         return [
    //             'status' => 'success',
    //             'data' => $results
    //         ];

    //     } catch (\PDOException $err) {
            
    //         return [
    //             'status' => 'error',
    //             'data' => $err->getMessage()
    //         ];
            
    //     }
    // }
}

?>