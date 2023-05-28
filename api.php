<?php
require_once __DIR__ . '/vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$secretKey = 'SecretKeyall';

try {
    $db = new PDO('pgsql:host=127.0.0.1;dbname=postgres', 'postgres', '1234');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $token = null;

// Проверяем наличие заголовка Authorization
    if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $token = $_SERVER['HTTP_AUTHORIZATION'];
    } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        $token = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
    } elseif (function_exists('apache_request_headers')) {
        $headers = apache_request_headers();
        if (isset($headers['Authorization'])) {
            $token = $headers['Authorization'];
        }
    }
    $input = $token ;
    $toDelete = 7;
    mb_internal_encoding("UTF-8");
    $token = mb_substr( $input, $toDelete);

    if ($token !== null) {

        $decodedToken = JWT::decode($token, new Key($secretKey, 'HS256'));


        $userId = $decodedToken->sub;

        // Получение всех данных опросов с ответами и дополнительными ответами для текущего пользователя
        $query = "SELECT s.id, s.title, s.date_create, s.status, s.user_id, s.count, 
              ARRAY_AGG(a.answer) AS answers, ARRAY_AGG(ca.answer_id) AS answer_ids
              FROM survey s
              LEFT JOIN answers a ON s.id = a.survey_id
              LEFT JOIN c_answer ca ON s.user_id = ca.user_id AND s.id = ca.survay_id
              WHERE s.user_id = :user_id
              GROUP BY s.id, s.title, s.date_create, s.status, s.user_id, s.count";

        // Если передан параметр id, фильтруем по нему
        if (isset($_GET['id'])) {
            $query .= " AND s.id = :id";
        }

        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $userId);

        // Если передан параметр id, привязываем его к запросу
        if (isset($_GET['id'])) {
            $stmt->bindParam(':id', $_GET['id']);
        }

        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode($results);
    }else
    {

    }
} catch (PDOException $e) {
    exit('Database error: ' . $e->getMessage());
} catch (Exception $e) {
    exit('Token error: ' . $e->getMessage());
}
?>