<?php
require_once 'connection.php';
require_once 'functions.php';

$title = 'Дела в порядке - Аутентификация пользователя';

$errors = [];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = validate_auth_form($_POST, get_users($con));

    if (count($errors) === 0) {
        header('Location: http://' . $_SERVER['SERVER_NAME']);
    }
}

$auth_content = include_template('auth.php', [
    'errors' => $errors
]);
print($auth_content);
