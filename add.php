<?php
require_once 'connection.php';
require_once 'functions.php';

$title = 'Дела в порядке - Добавление задачи';
$user_id = '1';
$categories = get_categories($con, [$user_id]);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $errors = validate_task_form($_POST, $categories);

//    Сохранить данные в БД или показать ошибки
    if (count($errors) === 0) {

        $date = null;
        if (!empty($_POST['date'])) {
            $date = date('Y-m-d', strtotime($_POST['date']));
        }

        $file_url = null;
        if (!empty($_FILES['preview']['tmp_name'])) {
            $file_name = $_FILES['preview']['name'];
            $file_path = __DIR__ . '/uploads/';
            $file_url = '/uploads/' . $file_name;
            move_uploaded_file($_FILES['preview']['tmp_name'], $file_path . $file_name);
        }

        $task_new = db_add_task($con, [
            'name' => $_POST['name'],
            'date' => $date,
            'file' => $file_url,
            'category_id' => $_POST['project'],
            'user_id' => intval($user_id)
        ]);

        if ($task_new > 0) {
            header('Location: http://' . $_SERVER['SERVER_NAME']);
        }
    }
}


$task_form = include_template('add.php', [
    'categories' => $categories,
    'errors' => $errors
]);

$layout_content = include_template('layout.php', [
    'title' => $title,
    'categories' => $categories,
    'page_content' => $task_form
]);

print($layout_content);
