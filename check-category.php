<?php

session_start();

require_once 'functions.php';

// Проверка открытой сессии
if (isAuth()) {
    $user_id = $_SESSION['id'];
} else {
    header("HTTP/1.0 403 Forbidden");
    echo '<div>Требуется аутентификация пользователя, доступ запрещен <a href="index.php">Перейти на главную страницу</a></div>';
    exit;
}

$category_id = intval($_GET['category_id']);
$user_category = null;

foreach ($categories as $category) {
    if ($category['id'] === $category_id) {
        $user_category = $category;
        break;
    }
}

if ($user_category === null) {
    header('HTTP/1.1 404 Not Found', true, 404);
    echo 'Проект не найден';
    exit;
}

$task_items = get_tasks_by_category($con, [$user_id, $user_id, $category_id]);
