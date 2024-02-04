<?php
namespace AdasFinance\Controller;

use AdasFinance\Entity\User;
use AdasFinance\Repository\UserRepository;

class UserController
{
    /** UserRepository */
    private $userRepository;
    
    public function __construct() 
    {
        $this->userRepository = new UserRepository();
    }

    public function signupAction()
    {
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);

        require_once '../view/signup.html';
    }

    public function signupSubmitAction($parameters)
    {
        $this->validateNoFieldsMissing($parameters);
        $result = $this->userRepository->saveUser($parameters);

        if($result['status'] === 'success') {
            header('Location: login');
            exit;
        }
    }

    public function loginAction() 
    {
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);

        require_once '../view/login.html';
    }

    public function loginSubmitAction($parameters) 
    {    
        $this->validateNoFieldsMissing($parameters);

        $result = $this->userRepository->getUserByUsername($parameters->username);
        $user = $this->validateLogin($result, $parameters->password);
        
        $_SESSION['user'] = $user;
        header('Location: home');
    }
    
    public function logoutAction()
    {
        session_destroy();
        header('Location: login');
    }

    private function validateNoFieldsMissing($parameters)
    {
        foreach ($parameters as $key => $value) {
            if(!$value) {
                $_SESSION['error'] = 'Usuário ou senha inválidos';
                header('Location: ' . $parameters->action);
                exit;
            }
        }
    }

    private function validateLogin(array $result, string $password)
    {
        if($result['status'] === 'error') {
            header('Location: 404');
            exit;
        }
        
        if(count($result['data']) === 0) {
            $_SESSION['error'] = 'Usuário ou senha inválidos';
            header('Location: login');
            exit;
        }
        
        if(!password_verify($password, $result['data'][0]->password)) {
            $_SESSION['error'] = 'Usuário ou senha inválidos';
            header('Location: login');
            exit;
        }

        $userArray = json_decode(json_encode($result['data'][0]), true);
        $user = new User($userArray);

        return $user;
    }
}
?>