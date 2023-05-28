<?php

namespace application\models;

require_once __DIR__ . '/../../vendor/autoload.php';

use PDO;
use application\core\Model;

class SurveyModel extends Model
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
            WHERE q.user_id = :user_id";
        return $query;
    }

    public function getSurveyById($surveyId)
    {
        $dsn = 'pgsql:host=127.0.0.1;dbname=postgres';
        $username = 'postgres';
        $password = '1234';
        try {
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $pdo->prepare("SELECT * FROM survey WHERE id = :surveyId");
        $stmt->bindValue(':surveyId', $surveyId, PDO::PARAM_INT);
        $stmt->execute();


        $survey = $stmt->fetch(PDO::FETCH_ASSOC);

        return $survey;
        }catch (PDOException $e) {
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

    public function showAllSurveys()
    {

    }

    public function createSurvey($question, $status, $count, $answers)
    {

        $user_id = $_SESSION['user_id'];
        try {
            $db = new PDO('pgsql:host=127.0.0.1;dbname=postgres', 'postgres', '1234');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


            // Создание записи опроса в таблице "surveys"
            $query1 = "INSERT INTO survey (title, status, user_id, count) VALUES (:title, :status, :user_id, :count)";
            $stmt1 = $db->prepare($query1);
            $stmt1->bindParam(':title', $question);
            $stmt1->bindParam(':status', $status);
            $stmt1->bindParam(':user_id', $user_id);
            $stmt1->bindParam(':count', $count);
            $stmt1->execute();

            // Получение идентификатора нового опроса
            $surveyId = $db->lastInsertId();

            // Вставка записей ответов в таблицу "answers"
            $query2 = "INSERT INTO answers (survey_id, answer) VALUES (:survey_id, :answer)";
            $stmt2 = $db->prepare($query2);
            $stmt2->bindParam(':survey_id', $surveyId);

            foreach ($answers as $answer) {
                $stmt2->bindParam(':answer', $answer);
                $stmt2->execute();
            }

            echo "Опрос создан. ID: " . $surveyId;

        } catch (PDOException $e) {
            // Откат транзакции в случае ошибки
            $db->rollBack();

            exit('Database error: ' . $e->getMessage());
        }
    }

    public function updateSurvey($surveyId, $question, $answers)
    {
        try {
            $db = new PDO('pgsql:host=127.0.0.1;dbname=postgres', 'postgres', '1234');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Обновление вопроса в таблице "survey"
            $query = "UPDATE survey SET title = :question WHERE id = :surveyId";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':question', $question);
            $stmt->bindParam(':surveyId', $surveyId);
            $stmt->execute();

            // Удаление старых ответов из таблицы "answers" для данного опроса
            $query = "DELETE FROM answers WHERE survey_id = :surveyId";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':surveyId', $surveyId);
            $stmt->execute();

            // Вставка новых ответов в таблицу "answers"
            $query = "INSERT INTO answers (survey_id, answer) VALUES (:surveyId, :answer)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':surveyId', $surveyId);

            foreach ($answers as $answer) {
                $stmt->bindParam(':answer', $answer);
                $stmt->execute();
            }

            echo "Опрос успешно обновлен.";

        } catch (PDOException $e) {
            exit('Database error: ' . $e->getMessage());
        }
    }

    public function deleteSurvey($surveyId)
    {
        try {
            $db = new PDO('pgsql:host=127.0.0.1;dbname=postgres', 'postgres', '1234');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $query = "DELETE FROM c_answer WHERE survay_id = :surveyId";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':surveyId', $surveyId);
            $stmt->execute();

            // Удаление ответов опроса из таблицы "answers"
            $query = "DELETE FROM answers WHERE survey_id = :surveyId";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':surveyId', $surveyId);
            $stmt->execute();

            // Удаление опроса из таблицы "survey"
            $query = "DELETE FROM survey WHERE id = :surveyId";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':surveyId', $surveyId);
            $stmt->execute();

            echo "Опрос успешно удален.";

        } catch (PDOException $e) {
            exit('Database error: ' . $e->getMessage());
        }
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

}
