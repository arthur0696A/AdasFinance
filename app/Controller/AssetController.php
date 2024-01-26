<?php
namespace AdasFinance\Controller;

use AdasFinance\Repository\UserAssetRepository;

class AssetController
{
    /** UserAssetRepository */
    private $userAssetRepository;
    
    public function __construct() 
    {
        $this->userAssetRepository = new UserAssetRepository();
    }

    public function homeAction()
    {
        include '../view/home.html';

        $userId = $_SESSION['user']->getId();
        $assets = $this->userAssetRepository->getAssetsByUserId($userId);
        $assets = $this->handleResult($assets);

        $totalUserAmount = $this->userAssetRepository->getTotalAmountByUserId($userId);
        $totalUserAmount = $this->handleResult($totalUserAmount)[0]['total_amount'];

        foreach ($assets as &$asset) {
            $asset['total_value'] = $this->numberFormat($asset['quantity'] * $asset['last_price']);
            $asset['asset_price_difference'] = $this->numberFormat(($asset['last_price'] - $asset['average_price']) / $asset['average_price'] * 100);
            $asset['total_price_difference'] = $this->numberFormat($asset['last_price'] * 100 - $asset['average_price'] * 100);
            $asset['percentage'] = $this->numberFormat(($asset['last_price'] * 100 / $totalUserAmount) * 100);
            $asset['percentage_goal'] = $this->numberFormat($asset['percentage_goal']);
            $asset['average_price'] = $this->numberFormat($asset['average_price']);
            include '../view/asset.html';
        }

        include '../view/home-end.html';
    }

    private function handleResult(array $result)
    {
        $finalData = [];
        if (isset($result['data']) && is_array($result['data'])) {
            foreach ($result['data'] as $data) {
                $dataArray = json_decode(json_encode($data), true);
                $finalData[] = $dataArray;
            }
        }

        return $finalData;
    }

    private function numberFormat($value)
    {
        return number_format($value, 2, ',', '.');
    }
}
?>