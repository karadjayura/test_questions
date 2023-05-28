<?php
namespace application\controllers\auth;

require_once __DIR__ . '/../../../vendor/autoload.php';
use application\core\Controller;

class LogoutController
{
    public function logoutAction()
    {
        session_start();
        session_unset();
        session_destroy();

        // Перенаправление на страницу входа или на другую страницу
        header('Location: /application/views/layouts/default.php');
        exit();
    }
}