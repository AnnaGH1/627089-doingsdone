<?php
/**
 * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
 *
 * @param $link mysqli Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_stmt Подготовленное выражение
 */
function db_get_prepare_stmt($link, $sql, $data = []) {
    $stmt = mysqli_prepare($link, $sql);

    if ($data) {
        $types = '';
        $stmt_data = [];

        foreach ($data as $value) {
            $type = null;

            if (is_int($value)) {
                $type = 'i';
            }
            else if (is_string($value)) {
                $type = 's';
            }
            else if (is_double($value)) {
                $type = 'd';
            }

            if ($type) {
                $types .= $type;
                $stmt_data[] = $value;
            }
        }

        $values = array_merge([$stmt, $types], $stmt_data);

        $func = 'mysqli_stmt_bind_param';
        $func(...$values);
    }

    return $stmt;
}

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

/**
 * Функция получает ассоциативный массив категорий при наличии соединения
 * @param $con mysqli - ресурс соединения
 * @param $data array - данные для запроса - id пользователя
 * @return array - ассоциативный массив категорий или пустой массив
 */
function get_categories($con, $data)
{
    $sql = 'SELECT * FROM category WHERE user_id = ?';
    $stmt = db_get_prepare_stmt($con, $sql, $data);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result === false) {
        return [];
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Функция получает ассоциативный массив задач для пользователя и их категории при наличии соединения
 * @param $con mysqli - ресурс соединения
 * @param $data array - данные для запроса - id пользователя
 * @return array - ассоциативный массив задач или пустой массив
 */
function get_tasks($con, $data)
{
    $sql = 'SELECT task.*, category.name AS category_name, DATE_FORMAT(task.dt_due, "%d.%m.%Y") AS due FROM task 
            JOIN category ON category.id=task.category_id AND category.user_id = ? WHERE task.user_id = ?';
    $stmt = db_get_prepare_stmt($con, $sql, $data);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result === false) {
        return [];
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Функция создает URL с параметрами запроса
 * @param $script_name string - название скрипта, из которого вызывается функция
 * @param $key string - ключ параметра запроса
 * @param $value string - значение параметра запроса
 * @return $url string - URL с параметрами запроса
 */
function get_url($script_name, $key, $value)
{
    $_GET[$key] = $value;
    $query = http_build_query($_GET);
    $url = '/' . $script_name . '?' . $query;
    return $url;
}

/**
 * Функция добавляет URL с параметрами запроса для категории в БД
 * @param $con mysqli - ресурс соединения
 * @param $data array - данные для запроса
 * @return bool|int|string - id последнего запроса
 */
function db_set_category_url ($con, $data)
{
    $sql = 'UPDATE category SET url = ? WHERE id = ?';
    $stmt = db_get_prepare_stmt($con, $sql, $data);
    $result = mysqli_stmt_execute($stmt);
    if ($result) {
        $result = mysqli_insert_id($con);
    }
    return $result;
}

