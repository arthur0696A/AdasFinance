<?php
namespace AdasFinance\Service;

require_once '/home/arthur/Projects/AdasFinance/vendor/autoload.php';
use AdasFinance\Service\ConnectionCreator;
use PDO;

class DatabasePopulator {
    private $api_key;

    public function __construct($api_key) {
        $this->api_key = $api_key;
    }

    public function processAssets() {
        // Faz a chamada para obter a lista de siglas
        $ticker_list_url = 'https://api.hgbrasil.com/finance/ticker_list';
        $ticker_list_response = $this->callAPI($ticker_list_url);

        if (!$ticker_list_response || !isset($ticker_list_response['results'])) {
            return false;
        }

        $results = $ticker_list_response['results'];

        // Agrupa as siglas em grupos de 5
        $groups = array_chunk($results, 5);

        $insert_queries = [];

        $queries_executed = 0;

        // Para cada grupo de siglas, faz a chamada para obter os detalhes
        foreach ($groups as $group) {
            $symbols = implode(',', $group);
            $stock_price_url = 'https://api.hgbrasil.com/finance/stock_price?key=' . $this->api_key . '&symbol=' . $symbols;
            $stock_price_response = $this->callAPI($stock_price_url);

            if (!$stock_price_response || !isset($stock_price_response['results'])) {
                continue;
            }

            $asset_data = $stock_price_response['results'];

            foreach ($asset_data as $symbol => $data) {
                // Traduz o tipo de grupo
                $group_type = isset($data['kind']) ? $this->translateGroupType($data['kind']) : 0;
                $data['price'] = isset($data['price']) ? $data['price'] : 0;
                $data['name'] = isset($data['name']) ? str_replace(["'", ","], "", $data['name']) : "";

                // Prepara o texto do insert
                $insert_queries[] = "INSERT INTO Asset (symbol, name, last_price, group_type) VALUES ('{$symbol}', '{$data['name']}', {$data['price']}, {$group_type})";
                
                $queries_executed++;
            }

            // Exibe mensagem de progresso
            $progress_percentage = floor(($queries_executed / 1454) * 100);
            echo "Progresso: {$progress_percentage}% \n";
        }

        // Executa os inserts na tabela Asset
        $this->executeInserts($insert_queries);
       
        return true;
    }

    private function callAPI($url) {
        $response = file_get_contents($url);
        return json_decode($response, true);
    }

    private function translateGroupType($kind) {
        switch ($kind) {
            case 'stock':
                return 1;
            case 'fii':
                return 2;
            case 'bdr':
                return 4;
            default:
                return 3;
        }
    }

    private function executeInserts($insert_queries) {
        $pdo = ConnectionCreator::getConnection();
        $count = 1;
        try {
            foreach ($insert_queries as $query) {
                // Executa o insert no banco de dados
                $stmt = $pdo->prepare($query);
                $stmt->execute([]);
                $stmt->fetchAll(PDO::FETCH_CLASS);
                echo "$count - Sucesso no insert: $query \n";
                $count++;
            }    
            

        } catch (PDOException $err) {
            echo "Erro no insert: $query  \n";
        }
        die;
    }
}

$api_key = '0a38b78f';
$processor = new AssetProcessor($api_key);
$processor->processAssets();

?>
