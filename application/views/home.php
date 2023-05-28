<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <title>Мои опросы</title>
</head>
<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="/Home">Главная</a>
            </li>
            <li class="nav-item">
                <a href="/surveys" class="nav-link">Список опросов</a>
            </li>
            <li class="nav-item">
                <a href="/application/views/createSurvey.php" class="nav-link">Создать опрос</a>
            </li>
            <li class="nav-item">
                <a href="/logout" class="nav-link">Выйти</a>
            </li>
        </ul>
    </nav>
</header>
<body>
<div class="container">
    <h1>Мои опросы</h1>

    <div class="mb-3">
        <strong>Сортировать по:</strong>
        <a href="/surveys/sort/date_create">Дате создания</a> |
        <a href="/surveys/sort/title">Заголовку</a> |
        <a href="/surveys/sort/status">Статусу</a>
    </div>
    <?php foreach ($surveys as $survey) : ?>
        <div class="card mt-3">
            <div class="card-body">
                <h5 class="card-title"><?= $survey['title'] ?></h5>
                <h6 class="card-subtitle mb-2 text-muted">Статус: <?= $survey['status'] ?></h6>
                <h6 class="card-subtitle mb-2 text-muted">Дата создания: <?= $survey['date_create'] ?></h6>
                <h6 class="card-subtitle mb-2 text-muted">Ответы:</h6>
                <?php if (!empty($survey['answers'])) :
                    $answerCounts = [];
                    $isVoted = false;
                    ?>
                    <div class="list-group">
                        <?php foreach ($survey['answers'] as $answer) :
                            $param = $answer['answer_id'];
                            $answerText = $answer['answer'];
                            $voteCount = $answer['vote_count'];
                            $userIds = 0;
                            if(isset($answer['user_id_c'])) {
                                $userIds = [$answer['user_id_c']];
                            }
                            if (array_key_exists($answerText, $answerCounts)) {
                                $voteCount += $answerCounts[$answerText]['vote_count'];
                                $userIds = array_merge($userIds, $answerCounts[$answerText]['user_ids']);
                            }

                            $answerCounts[$answerText] = [
                                'vote_count' => $voteCount,
                                'user_ids' => $userIds
                            ];
                            if ($voteCount > 0 && in_array($answer['user_id'], $userIds)) {
                                $isVoted = true;
                            }
                        endforeach;

                        // Выводим уникальные ответы с актуальным счетчиком голосов
                        foreach ($answerCounts as $answerText => $countData) :
                            $voteCount = $countData['vote_count'];
                            $userIds = $countData['user_ids'];
                            ?>
                            <a href="#" class="list-group-item list-group-item-action survey-answer <?= $isVoted ? 'disabled' : '' ?>"
                               data-question="<?= $survey['id'] ?>" data-answer-id="<?= $param ?>"
                               data-answer="<?= $answerText ?>">
                                <?= $answerText ?>
                                <span class="vote-count">(<?= $voteCount ?>)</span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <p>Ответов нет.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        // Обработчик клика на варианте ответа
        $('.survey-answer').click(function (event) {
            event.preventDefault();
            let questionId = $(this).data('question');
            let answerId = $(this).data('answer-id');
            let answer = $(this).data('answer');

            // Блокировка кнопок после выбора
            if ($(this).hasClass('disabled')) {
                return; // Если кнопка заблокирована, ничего не делаем
            }
            $('.survey-answer').addClass('disabled');

            let questionContainer  = $(this).parent().parent().html('');

            // Отправка данных на сервер
            $.ajax({
                url: '/survey/submitAnswer',
                method: 'POST',
                data: {
                    questionId: questionId,
                    answerId: answerId,
                    answer: answer
                },
                async: false,
                success: function (response) {
                    // Обработка успешного ответа от сервера
                    alert('Ответ успешно отправлен!');

                    let questionTitle = '<h6 class="card-subtitle mb-2 text-muted">Вопрос: ' + response[0].title + '</h6>';
                    questionContainer.append(questionTitle);
                    let questiondate = '<h6 class="card-subtitle mb-2 text-muted">Дата создания: ' + response[0].date_create + '</h6>';
                    questionContainer.append(questiondate);
                    let questionstatus = '<h6 class="card-subtitle mb-2 text-muted">Статус: ' + response[0].status + '</h6>';
                    questionContainer.append(questionstatus);

                    let answersList = $('<ul></ul>');
                    for(let i = 0; i < parseInt(response['counts']); i++)
                        answersList.append('<li>' + response[i].answer + ': ' + response[i].vote_count + '</li>');
                    questionContainer.append(answersList);
                },

                error: function () {
                    alert('Произошла ошибка при отправке ответа.');
                }

            });
        });
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
