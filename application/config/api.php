<?php

$db = new PDO('pgsql:host=127.0.0.1;dbname=postgres', 'postgres', '1234');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Обработка запроса на получение списка опросов
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'getSurveys') {
    try {
        // Выполнение запроса на получение списка опросов
        $query = "SELECT * FROM survey";
        $stmt = $db->query($query);
        $surveys = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Отправка ответа в формате JSON
        header('Content-Type: application/json');
        echo json_encode($surveys);
    } catch (PDOException $e) {
        // Обработка ошибки запроса
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

// Обработка запроса на получение информации об отдельном опросе
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'getSurvey') {
    try {
        $surveyId = $_GET['surveyId'];

        // Выполнение запроса на получение информации об опросе с указанным идентификатором
        $query = "SELECT * FROM survey WHERE id = :surveyId";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':surveyId', $surveyId);
        $stmt->execute();
        $survey = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($survey) {
            // Отправка ответа в формате JSON
            header('Content-Type: application/json');
            echo json_encode($survey);
        } else {
            // Отправка ответа с сообщением об ошибке
            echo json_encode(['error' => 'Survey not found']);
        }
    } catch (PDOException $e) {
        // Обработка ошибки запроса
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}