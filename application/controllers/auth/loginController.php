<?php


namespace application\controllers\auth;
require_once __DIR__ . '/../../../vendor/autoload.php';
use application\core\Controller;
use PDO;

class loginController extends Controller
{
    public function index()
    {
        if (isset($_SESSION['user_id'])) {
            // Если сессия активна, перенаправить на другую страницу
            header('Location: /home'); // Измените путь на нужный URL-адрес
            exit();
        }

        $data = array(
            'title' => 'Login',
        );

        $this->view->render('auth/LoginView', $data);
    }

    public function loginAction()
    {
        session_start();
        if (isset($_SESSION['user_id'] )) {

            header('Location: /application/views/layouts/default.php');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Получение данных из формы
            $email = $_POST['email'];
            $password = $_POST['password'];
            // Валидация данных
            if (empty($email) || empty($password)) {
                  //var_dump($email,$password);
                // Если поля username и password пустые, выведите ошибку или выполните соответствующие действия
                // Например, можно перенаправить обратно на страницу входа с сообщением об ошибке
                header('Location: /auth/login?error=empty_fields');
                exit();
            }

            // Проверка аутентификации
            // Здесь вы можете выполнить проверку соответствия введенного имени пользователя и пароля
            // с данными в вашей системе аутентификации (например, базой данных или другим хранилищем)

            $user = $this->authenticate($email, $password);
            if ($user) {
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                // Выполните необходимые действия, такие как сохранение данных сессии и перенаправление на защищенную страницу
                header('Location: /application/views/layouts/default.php');
                exit();
            } else {
                header('Location: /auth/login?error=invalid_credentials');
                exit();
            }
        }
    }

    private function authenticate($email, $password)
    {

        try {
            $db = new PDO('pgsql:host=127.0.0.1;dbname=postgres', 'postgres', '1234');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Обработка ошибки подключения к базе данных
            // Выведите ошибку или выполните соответствующие действия
            exit('Database connection error');
        }

        // Выполнение запроса на проверку аутентификации
        $query = 'SELECT * FROM users WHERE email = :email';
        $stmt = $db->prepare($query);
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        } else {
            //var_dump($password,$user);
            return false;
        }
    }
    public function checkAcl()
    {
        // Проверка прав доступа для конкретного контроллера и действия
        // Верните true или false в зависимости от условий доступа
        return true;
    }
}