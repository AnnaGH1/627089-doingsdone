<?php

date_default_timezone_set("Europe/Moscow");

require_once 'connection.php';
require_once 'functions.php';

$title = 'Дела в порядке';
$show_complete_tasks = rand(0, 1);
$task_items = get_tasks($con);

$page_content = include_template('index.php', [
    'show_complete_tasks' => $show_complete_tasks,
    'task_items' => $task_items
]);


$layout_content = include_template('layout.php', [
    'title' => $title,
    'categories' => get_categories($con),
    'task_items' => $task_items,
    'page_content' => $page_content
]);

print($layout_content);

