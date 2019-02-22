<?php

date_default_timezone_set("Europe/Moscow");

require_once 'connection.php';
require_once 'functions.php';

$title = 'Дела в порядке';
$show_complete_tasks = rand(0, 1);

$user_id = '1';

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
//    'task_items' => $task_items,
    'page_content' => $page_content
]);

print($layout_content);

