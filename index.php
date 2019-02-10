<?php
// показывать или нет выполненные задачи
$title = 'Дела в порядке';
$show_complete_tasks = rand(0, 1);
$category_names = ['Входящие', 'Учеба', 'Работа', 'Домашние дела', 'Авто'];
$task_items = [
    [
        'name' => 'Собеседование в IT компании',
        'date' => '01.12.2019',
        'category_name' => 'Работа',
        'done' => false
    ],
    [
        'name' => 'Выполнить тестовое задание',
        'date' => '25.12.2019',
        'category_name' => 'Работа',
        'done' => false
    ],
    [
        'name' => 'Сделать задание первого раздела',
        'date' => '21.12.2019',
        'category_name' => 'Учеба',
        'done' => true
    ],
    [
        'name' => 'Встреча с другом',
        'date' => '22.12.2019',
        'category_name' => 'Входящие',
        'done' => false
    ],
    [
        'name' => 'Купить корм для кота',
        'date' => 'Нет',
        'category_name' => 'Домашние дела',
        'done' => false
    ],
    [
        'name' => 'Заказать пиццу',
        'date' => 'Нет',
        'category_name' => 'Домашние дела',
        'done' => false
    ]
];

function calculate_tasks_by_category ($task_list, $task_category) {
    $counter = 0;
    foreach ($task_list as $task_item) {
        if ($task_item['category_name'] === $task_category) {
            $counter += 1;
        }
    }
    return $counter;
};


require_once('functions.php');

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

