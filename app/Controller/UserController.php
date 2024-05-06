<?php

namespace AdasFinance\Controller;

use AdasFinance\Entity\User;
use AdasFinance\Repository\AssetRepository;
use AdasFinance\Repository\UserRepository;
use AdasFinance\AlphaVantage\AlphaVantageApiService;

class UserController
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

    public function signupAction()
    {
        $error = $_SESSION['error'] ?? null;

        require_once '../view/signup.html';
    }

    public function signupSubmitAction($parameters)
    {
        $this->validateNoFieldsMissing($parameters);
        $result = $this->userRepository->saveUser($parameters);

        if ($result['status'] === 'success') {
            header('Location: login');
            exit;
        }
    }

    public function loginAction()
    {
        $error = $_SESSION['error'] ?? null;

        require_once '../view/login.html';
    }

    public function loginSubmitAction($parameters)
    {
        $this->validateNoFieldsMissing($parameters);

        $user = $this->userRepository->getUserByUsername($parameters->username);
        $this->validateLogin($user, $parameters->password);

        $_SESSION['user'] = $user;
        header('Location: home');
    }

    public function logoutAction()
    {
        session_destroy();
        header('Location: login');
    }

    public function synchronizeUserAssetsAction($parameters)
    {
        try {
            $assets = $this->userRepository->getAllUserAssetSymbols($parameters->userId);

            foreach ($assets as $asset) {
                $asset->setLastPrice(AlphaVantageApiService::getLastPrice($asset));
                $this->assetRepository->update($asset);
            }

            return json_encode(['success' => true, 'message' => 'Ativos sincronizados com sucesso']);
        } catch (\Exception $exception) {
            return json_encode(['success' => false, 'message' => 'Erro ao sincronizar ativos']);
        }

    }

    private function validateNoFieldsMissing($parameters)
    {
        foreach ($parameters as $key => $value) {
            if (!$value) {
                $_SESSION['error'] = 'Usuário ou senha inválidos';
                header('Location: ' . $parameters->action);
                exit;
            }
        }
    }

    private function validateLogin(?User $user = null, string $password)
    {
        if (!$user) {
            $_SESSION['error'] = 'Usuário ou senha inválidos';
            header('Location: login');
            exit;
        }

        if (!password_verify($password, $user->getPassword())) {
            $_SESSION['error'] = 'Usuário ou senha inválidos';
            header('Location: login');
            exit;
        }

        return $user;
    }

}

?>