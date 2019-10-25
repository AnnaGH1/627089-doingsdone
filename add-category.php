<?php

session_start();

require_once 'connection.php';
require_once 'functions.php';

$title = 'Doingsdone - Add project';
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

    $errors = validate_category_form($_POST, $categories);

    // Save to database or show errors
    if (count($errors) === 0) {
        $category = $_POST['name'];

        $category_new = db_add_category($con, [
            'category' => $category,
            'user_id' => intval($user_id)
        ]);

        if ($category_new > 0) {
            header('Location: http://' . $_SERVER['SERVER_NAME']);
        }
    }
}



$project_form = include_template('add-category.php', [
    'errors' => $errors
]);

$layout_content = include_template('layout.php', [
'title' => $title,
'categories' => $categories,
'page_content' => $project_form,
'user_name' => $user_name
]);

print($layout_content);
