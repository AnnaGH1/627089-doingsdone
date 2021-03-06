<?php

session_start();

require_once 'connection.php';
require_once 'functions.php';

$title = 'Doingsdone - Add task';
$user_id = null;
$user_name = null;

// Check open session
if (isset($_SESSION['id'])) {
    $user_id = $_SESSION['id'];
    $user_name = $_SESSION['name'];
} else {
    header("HTTP/1.0 403 Forbidden");
    echo '<div>User authentication required, access denied <a href="index.php">Go to Home page</a></div>';
    exit;
}

$categories = get_categories($con, [$user_id]);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $errors = validate_task_form($_POST, $categories);

    // Save to database or show errors
    if (count($errors) === 0) {
        $category_id = null;
        if (!empty($_POST['project'])) {
            $category_id = intval($_POST['project']);
        }

        $date = null;
        if (!empty($_POST['date'])) {
            $date = date('Y-m-d', strtotime($_POST['date']));
        }

        $file_url = null;
        $file_path = __DIR__ . '/uploads/';
        if (!is_dir($file_path)) {
            mkdir($file_path, 0777);
        }

        if (!empty($_FILES['preview']['tmp_name'])) {
            $file_name = $_FILES['preview']['name'];
            $file_url = '/uploads/' . $file_name;
            move_uploaded_file($_FILES['preview']['tmp_name'], $file_path . $file_name);
        }

        $task_new = db_add_task($con, [
            'name' => $_POST['name'],
            'date' => $date,
            'file' => $file_url,
            'category_id' => $category_id,
            'user_id' => intval($user_id)
        ]);

        if ($task_new > 0) {
            header('Location: http://' . $_SERVER['SERVER_NAME']);
        }
    }
}


$task_form = include_template('add-task.php', [
    'categories' => $categories,
    'errors' => $errors
]);

$layout_content = include_template('layout.php', [
    'title' => $title,
    'categories' => $categories,
    'page_content' => $task_form,
    'user_name' => $user_name
]);

print($layout_content);
