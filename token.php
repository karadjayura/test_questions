<?php
require_once __DIR__ . '/vendor/autoload.php';
use Firebase\JWT\JWT;

// Ключ для подписи токена (следует хранить в безопасном месте)
$secretKey = 'SecretKeyall';

session_start();

$userData = [
    'user_id' => $_SESSION['user_id'],
    'username' => $_SESSION['email']
];


$tokenPayload = [
    'sub' => $userData['user_id'],
    'username' => $userData['username'],
    'exp' => time() + 3600 // Время истечения токена (здесь устанавливается 1 час)
];
$jwt = JWT::encode($tokenPayload, $secretKey, 'HS256');

$responseData = [
    'token' => $jwt,
    'user_id' => $userData['user_id'],
    'username' => $userData['username']
];

// Отправка ответа клиенту
header('Content-Type: application/json');
echo json_encode($responseData);