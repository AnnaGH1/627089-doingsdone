<?php
require_once 'connection.php';
require_once 'functions.php';

$title = 'Дела в порядке - Регистрация пользователя';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $errors = validate_register_form($_POST, get_users($con));

//    Сохранить данные в БД или показать ошибки
    if (count($errors) === 0) {
        $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $user_new = db_add_user($con, [
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'password' => $password_hash
        ]);

        if ($user_new > 0) {
            header('Location: http://' . $_SERVER['SERVER_NAME']);
        }
    }
}


$register_form = include_template('register.php', [
    'errors' => $errors
]);

print($register_form);

