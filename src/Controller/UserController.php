<?php
namespace AdasFinance\Controller;

use AdasFinance\Repository\UserRepository;

class UserController
{
    /** UserRepository */
    private $userRepository;
    
    public function __construct() 
    {
        $this->userRepository = new UserRepository();
    }

    public function signinAction() 
    {
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);

        require_once '../view/signin.html';
    }

    public function loginAction() 
    {    
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /?route=signin');
            exit;
        }
        
        $username = $_POST['username'] ?? null;
        $password = $_POST['password'] ?? null;

        if(!$username || !$password) {
            header('Location: /?route=signin');
            exit;
        }

        $user = $this->userRepository->getUser($username);

        if(!$user) {
            $_SESSION['error'] = 'Usuário ou senha inválidos';
            header('Location: index.php?route=signin');
            exit;
        }

        if(!password_verify($password, $user->getPassword())) {
            $_SESSION['error'] = 'Usuário ou senha inválidos';
            header('Location: index.php?route=signin');
            exit;
        }

        $_SESSION['user'] = $user;

        header('Location: index.php?route=home');
    }
}

?>