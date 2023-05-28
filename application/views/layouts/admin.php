<!DOCTYPE html>
<html>
<head>
    <title>Личный кабинет</title>
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
                <a class="nav-link" href="#">Войти</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Кабинет</a>
            </li>
        </ul>
    </nav>
</header>

<div class="container mt-5">
    <h1>Личный кабинет</h1>

    <h2>Мои опросы</h2>
    <ul class="list-group">
        <li class="list-group-item">Опрос 1</li>
        <li class="list-group-item">Опрос 2</li>
        <li class="list-group-item">Опрос 3</li>
    </ul>

    <h2>Создать новый опрос</h2>
    <form action="#" method="post">
        <div class="form-group">
            <label for="question">Вопрос:</label>
            <input type="text" class="form-control" id="question" name="question" required>
        </div>

        <div class="form-group">
            <label for="options">Варианты ответов (через запятую):</label>
            <input type="text" class="form-control" id="options" name="options" required>
        </div>

        <button type="submit" class="btn btn-primary">Создать</button>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>