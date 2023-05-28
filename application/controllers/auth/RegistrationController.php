<?php
namespace application\controllers\auth;

require_once __DIR__ . '/../../../vendor/autoload.php';
use application\models\UserModel ;
use PDO;



class RegistrationController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function register()
    {
        // Обработка POST-запроса при отправке формы регистрации
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Получение данных из формы
            $email = $_POST['email'];
            $password = $_POST['password'];

            // Валидация данных и обработка ошибок

            // Создание нового пользователя
            $userId = $this->userModel->createUser($email, $password);

            // Проверка успешности создания пользователя
            if ($userId) {
                // Редирект на страницу успешной регистрации
                session_start();
                header('Location: /application/views/layouts/default.php');
                exit();
            } else {
                // Обработка ошибки создания пользователя
                $error = 'Ошибка при регистрации пользователя';
                include 'path/to/registration.html'; // Замените 'path/to/registration.html' на путь к вашему HTML-файлу
            }
        } else {
            // Отображение формы регистрации
            include 'path/to/registration.html'; // Замените 'path/to/registration.html' на путь к вашему HTML-файлу
        }
    }
}

// Использование контроллера
$registrationController = new RegistrationController();
$registrationController->register();