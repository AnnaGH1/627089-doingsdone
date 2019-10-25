<?php

require_once 'functions.php';

// Check open session
if (isAuth()) {
    $user_id = $_SESSION['id'];
} else {
    header("HTTP/1.0 403 Forbidden");
    echo '<div>User authentication required, access denied <a href="index.php">Go to Home page</a></div>';
    exit;
}

$task_id = intval($_GET['task_id']);
$user_task = null;

foreach ($task_items as $task_item) {
    if ($task_item['id'] === $task_id) {
        $user_task = $task_item;
        break;
    }
}

if ($user_task === null) {
    header('HTTP/1.1 404 Not Found', true, 404);
    echo 'Задача не найдена';
}
