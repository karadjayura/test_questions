<?php

namespace application\models;

require_once __DIR__ . '/../../vendor/autoload.php';

use PDO;
use application\core\Model;

class HomeModel extends Model
{
    protected $pdo;

    public function __construct()
    {

        session_start();
        if (!isset($_SESSION['user_id'])) {

            header('Location: /application/views/layouts/default.php');
            exit;
        }

        $dsn = 'pgsql:host=127.0.0.1;dbname=postgres';
        $username = 'postgres';
        $password = '1234';

        try {

            $this->pdo = new PDO($dsn, $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Обработка ошибки подключения к базе данных
            throw new Exception('Failed to connect to the database: ' . $e->getMessage());
        }
    }
    public function getAllSurveys()
    {
        $query = "SELECT q.id AS survey_id, q.title, q.date_create, q.status, q.user_id, q.count, a.id AS answer_id, a.answer, c.answer_id AS c_answer_id,  c.user_id AS user_id_c 
            FROM survey q
            LEFT JOIN answers a ON q.id = a.survey_id
            LEFT JOIN c_answer c ON a.id = c.answer_id AND c.survay_id = q.id
         ";
        return $query;
    }
    public function saveAnswer($questionId, $answer, $user)
    {
        $dsn = 'pgsql:host=127.0.0.1;dbname=postgres';
        $username = 'postgres';
        $password = '1234';

        try {
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $pdo->prepare('INSERT INTO c_answer (survay_id, answer_id, user_id) VALUES (?, ?, ?)');
            $stmt->execute([$questionId, $answer, $user]);

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
    public function getVoteCounts($questionId)
    {
        $dsn = 'pgsql:host=127.0.0.1;dbname=postgres';
        $username = 'postgres';
        $password = '1234';

        try {
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            if ($questionId !== NULL) {
                $stmt = $pdo->prepare("SELECT a.id,
            (select count(ca.answer_id) from c_answer ca2 order by answer_id limit 1) as vote_count,
            a.survey_id, 
            a.answer, 
            s.title, 
            s.status, 
            s.date_create
            FROM c_answer ca
            FULl JOIN answers a ON ca.answer_id = a.id
            full JOIN survey s ON a.survey_id = s.id
            where survey_id = $questionId
            GROUP BY a.id, a.survey_id, a.answer, s.title, s.status, s.date_create");

                $stmt->execute();
            } else {

                $stmt = $pdo->prepare('SELECT a.id, COUNT(*) AS vote_count, a.survey_id, a.answer, s.title, s.status, s.date_create
            FROM c_answer ca
            JOIN answers a ON ca.answer_id = a.id
            JOIN survey s ON a.survey_id = s.id
            GROUP BY a.id, a.survey_id, a.answer, s.title, s.status, s.date_create');

                $stmt->execute();
            }


            // Возвращаем результат запроса
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    public function getCountAnwer()
    {
        $dsn = 'pgsql:host=127.0.0.1;dbname=postgres';
        $username = 'postgres';
        $password = '1234';

        try {
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $pdo->prepare("SELECT survay_id,answer_id, user_id as user_id_for_check, COUNT(answer_id) AS count
            FROM c_answer
            WHERE answer_id IN (
                SELECT id
                FROM answers
            )
            GROUP BY answer_id, user_id_for_check,survay_id
            ");

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return [];
        }
    }

}