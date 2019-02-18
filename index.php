<?php
// показывать или нет выполненные задачи
$title = 'Дела в порядке';
$show_complete_tasks = rand(0, 1);
//$category_names = ['Входящие', 'Учеба', 'Работа', 'Домашние дела', 'Авто'];
//$task_items = [
//    [
//        'name' => 'Собеседование в IT компании',
//        'due_date' => '01.12.2019',
//        'category_name' => 'Работа',
//        'done' => false
//    ],
//    [
//        'name' => 'Выполнить тестовое задание',
//        'due_date' => '25.12.2019',
//        'category_name' => 'Работа',
//        'done' => false
//    ],
//    [
//        'name' => 'Сделать задание первого раздела',
//        'due_date' => '21.12.2019',
//        'category_name' => 'Учеба',
//        'done' => true
//    ],
//    [
//        'name' => 'Встреча с другом',
//        'due_date' => '22.12.2019',
//        'category_name' => 'Входящие',
//        'done' => false
//    ],
//    [
//        'name' => 'Купить корм для кота',
//        'due_date' => 'Нет',
//        'category_name' => 'Домашние дела',
//        'done' => false
//    ],
//    [
//        'name' => 'Заказать пиццу',
//        'due_date' => 'Нет',
//        'category_name' => 'Домашние дела',
//        'done' => false
//    ],
//    [
//        'name' => 'Купить корм для попугая',
//        'due_date' => '09.02.2019',
//        'category_name' => 'Домашние дела',
//        'done' => false
//    ]
//];


$con = mysqli_connect('localhost', 'root', '', '627089-doingsdone');


if ($con == false) {
    print('Ошибка подключения: ' . mysqli_connect_error());
}
else {
    print('connection established');

    mysqli_set_charset($con, 'utf8');
    $query_categories = 'SELECT name FROM category WHERE user_id = 1';
    $result_categories = mysqli_query($con, $query_categories);
    if (!$result_categories) {
        $error = mysqli_error($con);
        print('Ошибка MySQL: ' . $error);
    }
    $category_names = mysqli_fetch_all($result_categories, MYSQLI_NUM);

    $query_tasks = 'SELECT name FROM task WHERE user_id = 1';
    $result_tasks = mysqli_query($con, $query_tasks);
    if (!$result_tasks) {
        $error = mysqli_error($con);
        print('Ошибка MySQL: ' . $error);
    }
    $task_items = mysqli_fetch_all($result_tasks, MYSQLI_ASSOC);
}




date_default_timezone_set("Europe/Moscow");
require_once'functions.php';

$page_content = include_template('index.php', [
    'show_complete_tasks' => $show_complete_tasks,
    'task_items' => $task_items
]);


$layout_content = include_template('layout.php', [
    'title' => $title,
    'category_names' => $category_names,
    'task_items' => $task_items,
    'page_content' => $page_content
]);

print($layout_content);

