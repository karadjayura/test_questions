<!DOCTYPE html>
<html>
<head>
    <title>Создание опроса</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
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
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>Создание опроса</h2>
            <form method="post" action="/SurveyCreate">
                <div class="form-group">
                    <label for="title">Заголовок:</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="count">Количество ответов:</label>
                    <input type="number" class="form-control" id="count" name="count" required>
                </div>
                <div id="answers-container">
                    <!-- Строки для ввода ответов будут добавлены здесь -->
                </div>
                <div class="form-group">
                    <label for="status">Статус:</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="draft">Черновик</option>
                        <option value="published">Опубликован</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Создать</button>
            </form>
        </div>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        // Обработчик события изменения количества ответов
        $('#count').on('change', function() {
            var count = parseInt($(this).val());
            var container = $('#answers-container');
            var existingRows = container.children('.form-group');
            if (existingRows.length > count) {
                // Удаляем лишние строки для ввода ответов
                existingRows.slice(count).remove();
            } else if (existingRows.length < count) {
                // Добавляем новые строки для ввода ответов
                for (var i = existingRows.length + 1; i <= count; i++) {
                    var answerRow = '<div class="form-group">' +
                        '<label for="answer-' + i + '">Ответ ' + i + ':</label>' +
                        '<input type="text" class="form-control" id="answer-' + i + '" name="answer[]" required>' +
                        '</div>';
                    container.append(answerRow);
                }
            }
        });
    });
</script>
</body>
</html>