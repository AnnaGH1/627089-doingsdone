<?php

date_default_timezone_set("Europe/Moscow");

require_once 'connection.php';
require_once 'functions.php';

$title = 'Дела в порядке';
$show_complete_tasks = rand(0, 1);

$user_id = '1';

$task_items = get_tasks($con, [$user_id, $user_id]);
$categories = get_categories($con, [$user_id]);

$script_name = pathinfo(__FILE__, PATHINFO_BASENAME);

foreach ($categories as $category) {
    $url = get_url($script_name,'id', $category['id']);
    db_set_category_url($con, [$url, $category['id']]);
}

$page_content = include_template('index.php', [
    'show_complete_tasks' => $show_complete_tasks,
    'task_items' => $task_items
]);


$layout_content = include_template('layout.php', [
    'title' => $title,
    'categories' => $categories,
    'task_items' => $task_items,
    'page_content' => $page_content
]);

print($layout_content);

$filter_field = '';

//if (isset($_GET['category.id'] && $_GET['category.id'] === '1')) {
//    $filter_field = 'category.id';
//    print 'id 1 selected';
//} else {
//    print 'other category selected';
//}
