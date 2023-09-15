<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>HTML Document</title>
</head>
<body>

<h3>Здравствуйте {{$name . " " . $patronymic}}, {{$header}}</h3>

<p>Проверте корректность ваших данных:</p>

<ul>
    <li>ФИО: <b>{{$surname ." ". $name." ".  $patronymic}}</b></li>
    <li>Дата рождения: <b>{{ $birth }}</b></li>
    <li>Почта: <b>{{$email}}</b></li>
    <li>Серия-номер паспорта: <b>{{$passport}}</b></li>
    <li>Телефон: <b>{{$number}}</b></li>
    <li>Адрес: <b>{{$registration}}</b></li>
</ul>

<p>В случае нахождения ошибки просим вас обратится к администратору.</p>

<p>Ваш пароль для входа в личный кабинет: {{$password}}</p>

</body>
</html>
