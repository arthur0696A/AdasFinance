<?php

namespace AdasFinance\Controller;

use AdasFinance\Repository\AssetRepository;
use AdasFinance\Repository\UserRepository;
use Exception;

class AssetController
{
    /** AssetRepository */
    private $assetRepository;

    /** UserRepository */
    private $userRepository;

    public function __construct()
    {
        $this->assetRepository = new AssetRepository();
        $this->userRepository = new UserRepository();
    }

    public function searchBySymbolAction($parameters)
    {
        $result = $this->assetRepository->searchByQuery($parameters->symbol, $_SESSION['user']->getId());

        echo json_encode($result);
    }
}

?>