<?php

if ($_SERVER['request_method'] === 'POST') {
    $task_new = $_POST;
    $errors = [];

//    Валидация поля с названием задачи
    if (empty($task_new['name'])) {
        $errors['name'] = 'Поле не заполнено';
    } else (ctype_space($task_new['name'])) {
        $errors['name'] =  'Поле не должно содержать только пробелы'
    };

//    Валидация поля с названием проекта
    if (!empty($task_new['project'])) {
        $user_category = $task_new['project'];
        $category_valid = false;

        foreach ($categories as $category) {
            if ($user_category === $category['name']) {
                $category_valid = true;
                break;
            }
        }

        if (!$category_valid) {
            $errors['project'] =  'Проект не существует';
        };
    }

//    Валидация поля с датой
    if (!empty($task_new['date'])) {
        if ($task_new['date'] <= time()) {
            $errors['date'] = 'Дата должна быть больше или равна текущей';
        }
    }

//    Вывод ошибок
    if (count($errors)) {
        echo 'Есть ошибки в заполнении формы';
    }
}
