<?php

/**
 * Функция подсчитывает число задач, относящихся к указанной категории
 * @param array $task_list - массив задач, каждая задача - ассоциативный массив и содержит ключ 'category_name'
 * @param string $task_category - значение ключа 'category_name'
 * @return int $counter - число задач
 */
function calculate_tasks_by_category ($task_list, $task_category) {
    $counter = 0;
    foreach ($task_list as $task_item) {
        if ($task_item['category_name'] === $task_category) {
            $counter += 1;
        }
    }
    return $counter;
};

/**
 * Функция проверяет срок выполнения задачи, если срок истекает в ближайшие 24 часа, возвращает true
 * @param array $task -
 * @return boolean $status_bool
 */
function is_task_important ($task) {
    if ($task['due_date'] === 'Нет') {
        return false;
    } else {
//        $time_due = '23:59:59';
//        $due_date_and_time = $task['due_date'] . ' ' . $time_due;
//        $due_date_and_time_ts = strtotime($due_date_and_time);
//        $current_ts = time();
//        $secs_in_hour = 3600;
//        $hours_before_due = floor(($due_date_and_time_ts - $current_ts) / $secs_in_hour);

        // Как лучше числа или булевы? булевы не выводятся в print?
        // $hours_before_due > 24 ? $status = 0 : $status = 1;
        // print 'status number ' . $status;

        $due_date = date_create($task['due_date']);
        $current_date = date_create(date('d.m.Y'));
        $diff_hours = date_diff($due_date, $current_date, $differenceFormat = '%h');
//        print $diff_hours;


        $diff_hours > 24 ? $status_bool = false : $status_bool = true;
        // print 'status bool ' . $status_bool;

        return $status_bool;
    }
};

is_task_important($task_items[0]);

/**
 * Функция подключает шаблон с данными
 * @param string $name - имя файла в папке 'templates'
 * @param array $data - ассоциативный массив, содержащий переменные для данного шаблона, имя ключа совпадает с именем переменной
 * @return string $result - пустая строка, если шаблон не существует, или код html
 */
function include_template($name, $data) {
$name = 'templates/' . $name;
$result = '';

if (!is_readable($name)) {
return $result;
}

ob_start();
extract($data);
require $name;

$result = ob_get_clean();

return $result;
}
