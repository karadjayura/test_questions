<?php

namespace application\controllers;
require_once __DIR__ . '/../../vendor/autoload.php';
use application\core\Controller;
use application\models\HomeModel;
use PDO;
class HomeController extends Controller
{
    protected $pdo;

    public function indexAction()
    {
        $surveyModel = new SurveyModel();

        $surveys = $surveyModel->getAllSurveys();

        $data = [
            'title' => 'Список опросов',
            'surveys' => $surveys
        ];

        $this->view->render('Home/index', $data);
    }

    public function getAllSurveysAction($sort = 'date_create')
    {
        try {
            $model = new HomeModel();
            $db = new PDO('pgsql:host=127.0.0.1;dbname=postgres', 'postgres', '1234');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $user = $_SESSION['user_id'];

            $query = $model->getAllSurveys();

            $stmt = $db->prepare($query);
            $stmt->execute();

            $surveys = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $countVotes = $model->getCountAnwer();
            $sur = [];
            $processedSurveys = [];
            foreach ($surveys as $survey) {
                if ($survey['status'] === 'draft') {
                    continue; // Пропускаем опросы со статусом "draft"
                }

                $surveyId = $survey['survey_id'];
                $answerId = isset($survey['answer_id']) ? $survey['answer_id'] : null;
                $answer = $survey['answer'];

                if (!isset($processedSurveys[$surveyId])) {
                    $processedSurveys[$surveyId] = [
                        'id' => $surveyId,
                        'title' => $survey['title'],
                        'status' => $survey['status'],
                        'date_create' => $survey['date_create'],
                        'answers' => [],
                    ];
                }

                if ($survey['c_answer_id'] !== NULL && isset($countVotes[0]['count'])) {
                    $param = $countVotes[0]['count'];

                    $processedSurveys[$surveyId]['answers'][] = [
                        'answer_id' => $answerId,
                        'answer' => $answer,
                        'user_id' => $user,
                        'vote_count' => $param,
                        'user_id_for_check' => $countVotes[0]['user_id_for_check'],
                        'survay_id' => $countVotes[0]['survay_id'],
                        'c_answer_id' => $survey['c_answer_id'],
                        'user_id_c' => $survey['user_id_c']
                    ];
                } else {
                    $processedSurveys[$surveyId]['answers'][] = [
                        'answer_id' => $answerId,
                        'answer' => $answer,
                        'user_id' => $user,
                        'vote_count' => 0,
                        'survay_id' => $surveyId,
                        'c_answer_id' => $survey['c_answer_id']
                    ];
                }
            }

            $data = [
                'surveys' => $processedSurveys
            ];

            // Включение файла view и передача данных через переменные
            ob_start();
            extract($data);
            include(__DIR__ . '/../views/home.php');
            ob_end_flush();
        } catch (PDOException $e) {
            exit('Database error: ' . $e->getMessage());
        }
    }
    public function submitAnswerAction()
    {
        header('Content-Type: application/json');
        $questionId = $_POST['questionId'];
        $answerId = $_POST['answerId'];

        $model = new SurveyModel();
        $user = $_SESSION['user_id'];

        // Вызов метода модели для сохранения ответа
        $success = $model->saveAnswer($questionId, $answerId, $user);

        if ($success) {
            // Получение информации о количестве проголосовавших людей для каждого ответа
            //$voteCounts = $model->getVoteCounts(null);
            $voteCounts = $model->getVoteCounts($questionId);
            $response = array();
            $index = 0;
            foreach($voteCounts as $row) {

                $response[$index]['id'] = $row['id'];
                $response[$index]['vote_count'] = $row['vote_count'];
                $response[$index]['survey_id'] = $row['survey_id'];
                $response[$index]['answer'] = $row['answer'];
                $response[$index]['title'] = $row['title'];
                $response[$index]['status'] = $row['status'];
                $response[$index]['date_create'] = $row['date_create'];
                $index++;
            }
            $response['counts'] = $index;

            echo json_encode($response);
        } else {
            // Формирование ответа в виде JSON в случае ошибки
            $response = ['success' => false];
            echo json_encode($response);
        }
    }
    public function checkAcl()
    {
        // Проверка прав доступа для конкретного контроллера и действия
        return true;
    }

}