<?php

namespace app\Controller;

class UserController
{
    public function loginAction() {
        require_once __DIR__ . 'view/login.html';
    }
}

?>