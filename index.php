<?php
session_start();

date_default_timezone_set("America/New_York");

require_once 'connection.php';
require_once 'functions.php';

$title = 'Doingsdone';
$show_complete_tasks = 0;

// If session not opened, show register and sign in page
if (!isAuth()) {
    $guest_content = include_template('guest.php', []);
    print($guest_content);
    exit;
}

if (isAuth()) {
    $user_id = $_SESSION['id'];
    $user_name = $_SESSION['name'];
}

// Show completed tasks
if (isset($_GET['show_completed']) && (intval($_GET['show_completed'])) === 1) {
    $show_complete_tasks = 1;
} else {
    $show_complete_tasks = 0;
}

$categories = get_categories($con, [$user_id]);
$task_items = get_tasks($con, [$user_id]);

// Search tasks
if (isset($_GET['query']) && (!empty(trim($_GET['query'])))) {
    $query = trim($_GET['query']);
    $task_items = get_tasks_by_query($con, [$query, $user_id]);

    if (count($task_items) === 0) {
        $task_items = [];
    }
}

// Filter tasks by projects
if (isset($_GET['category_id'])) {
    require_once 'check-category.php';
    $task_items = get_tasks_by_category($con, [$user_id, $user_id, $category_id]);
}

// Filter tasks
if (isset($_GET['dt_due']) && ($_GET['dt_due'] === 'all')) {
    if (isset($_GET['category_id'])) {
        require_once 'check-category.php';
        $task_items = get_tasks_by_category($con, [$user_id, $user_id, $category_id]);
    } else {
        $task_items = get_tasks($con, [$user_id]);
    }
}

if (isset($_GET['dt_due']) && ($_GET['dt_due'] === 'today')) {
    $today = date('Y-m-d');
    $task_items = get_tasks_by_due_date($con, [$user_id, $today]);
}

if (isset($_GET['dt_due']) && ($_GET['dt_due'] === 'tomorrow')) {
    $tomorrow = date('Y-m-d', (time() + 60 * 60 * 24));
    $task_items = get_tasks_by_due_date($con, [$user_id, $tomorrow]);
}

if (isset($_GET['dt_due']) && ($_GET['dt_due'] === 'overdue')) {
    $task_items = get_tasks_overdue($con, [$user_id]);
}

// Task completed
if (isset($_GET['task_id']) && $_GET['check'] === '1') {
    require_once 'check-task.php';
    db_add_dt_complete($con, [$task_id]);
    header('Location: http://' . $_SERVER['SERVER_NAME']);
}

// Task not completed
if (isset($_GET['task_id']) && $_GET['check'] === '0') {
    require_once 'check-task.php';
    db_remove_dt_complete($con, [$task_id]);
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

