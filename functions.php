<?php

/**
 * Функция подсчитывает число задач, относящихся к указанной категории
 * @param array $task_list - массив задач, каждая задача - ассоциативный массив и содержит ключ 'category_name'
 * @param string $task_category - значение ключа 'category_name'
 * @return int $counter - число задач
 */
function calculate_tasks_by_category ($task_list, $task_category)
{
    $counter = 0;
    foreach ($task_list as $task_item) {
        if ($task_item['category_name'] === $task_category) {
            $counter += 1;
        }
    }
    return $counter;
}

/**
 * Функция проверяет срок выполнения задачи, если срок истекает в ближайшие 24 часа, возвращает true
 * @param array $task - ассоциативный массив задачи
 * @return boolean $status - статус задачи
 */
function is_task_important ($task)
{
    if ($task['dt_due'] === NULL) {
        return false;
    } else {
        $due_date_and_time_ts = strtotime($task['dt_due']);
        $current_ts = time();
        $hours_before_due = floor(($due_date_and_time_ts - $current_ts) / 3600);
        $status = $hours_before_due > 24 ? false : true;
        return $status;
    }
}

/**
 * Функция подключает шаблон с данными
 * @param string $name - имя файла в папке 'templates'
 * @param array $data - ассоциативный массив, содержащий переменные для данного шаблона, имя ключа совпадает с именем переменной
 * @return string $result - пустая строка, если шаблон не существует, или код html
 */
function include_template($name, $data)
{
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
