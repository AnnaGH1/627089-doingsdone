<?php

require_once 'connection.php';
require_once 'functions.php';
require_once 'task-validate.php';

$title = 'Дела в порядке';

$user_id = '1';

$categories = get_categories($con, [$user_id]);

$task_form = include_template('add.php', [
    'categories' => $categories
]);


$layout_content = include_template('layout.php', [
    'title' => $title,
    'categories' => $categories,
    'page_content' => $task_form
]);

print($layout_content);
