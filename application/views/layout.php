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
    <?php
   // if(isset(($content)))
    {
   // echo $content;
    }
    ?>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>