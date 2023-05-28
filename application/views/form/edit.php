<!DOCTYPE html>
<html>
<head>
    <title>Обновление опроса</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="#">Главная</a>
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
            <h2>Обновление опроса</h2>
            <form method="post" action="/survey/edit/">
                <div class="form-group">
                    <label for="title">Заголовок:</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?php echo isset($survey['title']) ? $survey['title'] : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="count">Количество ответов:</label>
                    <input type="number" class="form-control" id="count" name="count" value="<?php echo isset($survey['count']) ? $survey['count'] : ''; ?>" required>
                </div>
                <div id="answers-container">
                    <?php if (isset($survey['answers']) && is_array($survey['answers'])): ?>
                        <?php foreach ($survey['answers'] as $index => $answer): ?>
                            <div class="form-group answer-row">
                                <label for="answer-<?php echo $index; ?>">Ответ <?php echo $index; ?>:</label>
                                <input type="text" class="form-control" id="answer-<?php echo $index; ?>" name="answer[]" value="<?php echo $answer; ?>" required>
                                <?php if ($index > 1): ?>
                                    <button type="button" class="btn btn-danger btn-sm remove-answer">Удалить</button>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <button type="button" class="btn btn-primary btn-sm" id="add-answer">Добавить ответ</button>
                </div>
                <div class="form-group">
                    <label for="status">Статус:</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="draft" <?php echo isset($survey['status']) && $survey['status'] === 'draft' ? 'selected' : ''; ?>>Черновик</option>
                        <option value="published" <?php echo isset($survey['status']) && $survey['status'] === 'published' ? 'selected' : ''; ?>>Опубликован</option>
                    </select>
                </div>
                <input type="hidden" name="survey_id" value="<?php echo isset($survey['id']) ? $survey['id'] : ''; ?>">
                <button type="submit" class="btn btn-primary">Сохранить</button>
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
            var existingRows = container.children('.answer-row');
            if (existingRows.length > count) {
                // Удаляем лишние строки для ввода ответов
                existingRows.slice(count).remove();
            } else if (existingRows.length < count) {
                // Добавляем новые строки для ввода ответов
                for (var i = existingRows.length + 1; i <= count; i++) {
                    var answerRow = '<div class="form-group answer-row">' +
                        '<label for="answer-' + i + '">Ответ ' + i + ':</label>' +
                        '<input type="text" class="form-control" id="answer-' + i + '" name="answer[]" required>' +
                        '<button type="button" class="btn btn-danger btn-sm remove-answer">Удалить</button>' +
                        '</div>';
                    container.append(answerRow);
                }
            }
        });

        // Добавление обработчика события для кнопки удаления ответа
        $(document).on('click', '.remove-answer', function() {
            $(this).closest('.answer-row').remove();
        });

        // Добавление обработчика события для кнопки добавления ответа
        $('#add-answer').on('click', function() {
            var container = $('#answers-container');
            var count = container.children('.answer-row').length + 1;
            var answerRow = '<div class="form-group answer-row">' +
                '<label for="answer-' + count + '">Ответ ' + count + ':</label>' +
                '<input type="text" class="form-control" id="answer-' + count + '" name="answer[]" required>' +
                '<button type="button" class="btn btn-danger btn-sm remove-answer">Удалить</button>' +
                '</div>';
            container.append(answerRow);
        });
    });
</script>
</body>
</html>