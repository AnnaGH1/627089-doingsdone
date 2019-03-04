<?php
session_start();

date_default_timezone_set("Europe/Moscow");

require_once 'connection.php';
require_once 'functions.php';

$title = 'Дела в порядке';

// Если открытой сессии нет, перенаправление на страницу аутентификации
if (!isAuth()) {
    $guest_content = include_template('guest.php', []);
    print($guest_content);
    exit;
}

if (isAuth()) {
    $user_id = $_SESSION['id'];
    $user_name = $_SESSION['name'];
}

// Фильтр показа выполненных задач
if (isset($_GET['show_completed']) && (intval($_GET['show_completed'])) === 1) {
    $show_complete_tasks = 1;
} else {
    $show_complete_tasks = 0;
}

$categories = get_categories($con, [$user_id]);
$task_items = get_tasks($con, [$user_id]);

// Фильтр задач по проектам
if (isset($_GET['category_id'])) {
    require_once 'check-category.php';
    $task_items = get_tasks_by_category($con, [$user_id, $user_id, $category_id]);
}

// Задача отмечена как выполненная
if (isset($_GET['task_id'])) {
    require_once 'check-task.php';
    db_add_dt_complete($con, [$task_id]);
    header('Location: http://' . $_SERVER['SERVER_NAME']);
}

$page_content = include_template('index.php', [
    'show_complete_tasks' => $show_complete_tasks,
    'task_items' => $task_items
]);


$layout_content = include_template('layout.php', [
    'title' => $title,
    'categories' => $categories,
    'page_content' => $page_content,
    'user_name' => $user_name
]);

print($layout_content);

