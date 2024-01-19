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

    public function signupSubmitAction()
    {
        $parameters = $this->validateNoFieldsMissing();
        $result = $this->userRepository->saveUser($parameters);

        if($result['status'] === 'success') {
            header('Location: index.php?route=login');
            exit;
        }
    }

    public function loginAction() 
    {
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);

        require_once '../view/login.html';
    }

    public function loginSubmitAction() 
    {    
        $parameters = $this->validateNoFieldsMissing();

        $result = $this->userRepository->getUserByUsername($parameters['username']);
        $user = $this->validateLogin($result, $parameters['password']);
        
        $_SESSION['user'] = $user;
        header('Location: index.php?route=home');
    }
    
    public function logoutAction()
    {
        session_destroy();
        header('Location: index.php?route=login');
    }

    private function validateNoFieldsMissing()
    {
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?route=' . $_POST['action']);
            exit;
        }

        foreach ($_POST as $key => $value) {
            if(!$value) {
                $_SESSION['error'] = 'Usuário ou senha inválidos';
                header('Location: index.php?route=' . $_POST['action']);
                exit;
            }
        }

        return $_POST;
    }

    private function validateLogin(array $result, string $password)
    {
        if($result['status'] === 'error') {
            header('Location: index.php?route=404');
            exit;
        }
        
        if(count($result['data']) === 0) {
            $_SESSION['error'] = 'Usuário ou senha inválidos';
            header('Location: index.php?route=login');
            exit;
        }
        
        if(!password_verify($password, $result['data'][0]->password)) {
            $_SESSION['error'] = 'Usuário ou senha inválidos';
            header('Location: index.php?route=login');
            exit;
        }

        $userArray = json_decode(json_encode($result['data'][0]), true);
        $user = new User($userArray);

        return $user;
    }
}
?>