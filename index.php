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

$show_complete_tasks = rand(0, 1);

$categories = get_categories($con, [$user_id]);

if (isset($_GET['category_id'])) {
    require_once 'category.php';
} else {
    $task_items = get_tasks($con, [$user_id, $user_id]);
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


