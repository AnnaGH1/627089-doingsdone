<?php
require_once 'connection.php';
require_once 'functions.php';

$title = 'Дела в порядке - Аутентификация пользователя';

$errors = [];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = validate_auth_form($_POST, get_users($con));
    $users = get_users($con);
    $user_valid = null;

    foreach ($users as $user) {
        if ($_POST['email'] === $user['email']) {
            $user_valid = $user;
            break;
        }
    }

    //    Валидация совпадения паролей
    if (!empty($_POST['email']) && !empty($_POST['password'])) {
        if (!password_verify($_POST['password'], $user_valid['password'])) {
            $errors['match'] = 'Вы ввели неверный email/пароль';
        }
    }

    if (count($errors) === 0) {
        session_start();
        $_SESSION['id'] = $user_valid['id'];
        $_SESSION['name'] = $user_valid['name'];
        $_SESSION['email'] = $user_valid['email'];
        header('Location: http://' . $_SERVER['SERVER_NAME']);
    }
}

$auth_content = include_template('auth.php', [
    'errors' => $errors
]);

print($auth_content);
