<?php

namespace application\models;

use application\core\Model;
use PDO;
class UserModel
{
    private $db;

    public function __construct()
    {
        // Подключение к базе данных
        $this->db = new PDO('pgsql:host=127.0.0.1;dbname=postgres', 'postgres', '1234');

        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function createUser( $email, $password)
    {
        try {
            // Хеширование пароля
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Вставка нового пользователя в базу данных
            $stmt = $this->db->prepare('INSERT INTO users (email, password) VALUES (?, ?)');
            $stmt->execute([ $email, $hashedPassword]);

            // Возвращение ID нового пользователя
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            // Обработка ошибки
            return false;
        }
    }


}