<?php

require_once 'connection.php';
require_once 'functions.php';

$title = 'Дела в порядке';

$user_id = '1';

$categories = get_categories($con, [$user_id]);


if (($_SERVER['REQUEST_METHOD'] === 'POST') && (!empty($_POST['action']))) {

    if (isset($_POST['name'])) {
        $name = trim($_POST['name']);
//        $name = filter_var($name, FILTER_SANITIZE_STRING); // add user message
    }

    if (isset($_POST['project'])) {
        $category = $_POST['project'];
    }

    if (isset($_POST['date'])) {
        $date = $_POST['date'];
    }

    if (isset($_FILES['preview'])) {
        $file = $_FILES['preview'];
    }

    $errors = [];

//    Валидация поля с названием задачи
    if (empty($name)) {
        $errors['name'] = 'Поле Название должно быть заполнено';
    }

    if(!preg_match('/[A-Za-z]+/', $name)) {
        $errors['name'] = 'Поле Название должно состоять из букв или букв и цифр';
    }


//    Валидация поля с названием проекта
    if (!empty($category)) {
        $user_category = intval($category);
        $category_valid = false;

        foreach ($categories as $category) {
            if ($user_category === $category['id']) {
                $category_valid = true;
                break;
            }
        }

        if ($category_valid === false) {
            $errors['project'] = 'Проект не существует';
        };
    }

//    Валидация поля с датой
    if (!empty($date)) {
        if ($date <= time()) {
            $errors['date'] = 'Дата должна быть больше или равна текущей';
        }
    }

//    Поле загрузки файла
    if (!empty($_FILES)) {
        var_dump($_FILES);
    }


//    Вывод ошибок
    if (count($errors)) {
        var_dump($errors);
    } else {
        header('Location: http://doingsdone/index.php');
    }
}


$task_form = include_template('add.php', [
    'categories' => $categories,
]);

$layout_content = include_template('layout.php', [
    'title' => $title,
    'categories' => $categories,
    'page_content' => $task_form
]);

print($layout_content);
