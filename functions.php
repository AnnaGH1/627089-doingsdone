<?php
/**
 * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
 *
 * @param mysqli $link Ресурс соединения
 * @param string $sql SQL запрос с плейсхолдерами вместо значений
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
            } else {
                $type = 's';
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
 * Функция получает ассоциативный массив пользователей
 * @param mysqli $con - ресурс соединения
 * @return array - ассоциативный массив пользователей или пустой массив
 */
function get_users($con)
{
    $sql = 'SELECT email, id FROM user';
    $stmt = db_get_prepare_stmt($con, $sql);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result === false) {
        return [];
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}


/**
 * Функция получает ассоциативный массив категорий
 * @param mysqli $con - ресурс соединения
 * @param array $data - данные для запроса - id пользователя
 * @return array - ассоциативный массив категорий или пустой массив
 */
function get_categories($con, $data)
{
    $sql = 'SELECT c.*, COUNT(t.id) AS tasks_count FROM category AS c 
            LEFT JOIN task AS t ON c.id = t.category_id WHERE c.user_id = ? GROUP BY c.id ORDER BY c.name ASC';
    $stmt = db_get_prepare_stmt($con, $sql, $data);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result === false) {
        return [];
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Функция получает ассоциативный массив задач для пользователя и их категории
 * @param mysqli $con - ресурс соединения
 * @param array $data - данные для запроса - id пользователя
 * @return array - ассоциативный массив задач или пустой массив
 */
function get_tasks($con, $data)
{
    $sql = 'SELECT task.*, category.name AS category_name, DATE_FORMAT(task.dt_due, "%d.%m.%Y") AS due FROM task 
            JOIN category ON category.id = task.category_id AND category.user_id = ? WHERE task.user_id = ? ORDER BY task.dt_add DESC';
    $stmt = db_get_prepare_stmt($con, $sql, $data);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result === false) {
        return [];
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Функция получает ассоциативный массив задач для пользователя в выбранной категории
 * @param mysqli $con - ресурс соединения
 * @param array $data - данные для запроса - id пользователя и id категории
 * @return array - ассоциативный массив задач или пустой массив
 */
function get_tasks_by_category($con, $data)
{
    $sql = 'SELECT task.*, category.name AS category_name, DATE_FORMAT(task.dt_due, "%d.%m.%Y") as due FROM task 
            JOIN category ON category.id = task.category_id AND category.user_id = ? WHERE task.user_id = ? AND category.id = ?';
    $stmt = db_get_prepare_stmt($con, $sql, $data);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result === false) {
        return [];
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Функция создает параметр запроса с ключом category_id
 * @param string $category_id - значение параметра запроса
 * @return string $query - параметр запроса
 */
function get_category_url($category_id)
{
    $params = $_GET;
    $params['category_id'] = $category_id;
    $query = http_build_query($params);
    return $query;
}

/**
 * Функция валидирует данные формы добавления задачи
 * @param array $data - данные из формы
 * @param array $categories - категории для списка проектов
 * @return array $errors - массив ошибок
 */
function validate_task_form ($data, $categories)
{
    $errors = [];

    //    Валидация поля с названием задачи
    if (empty(trim($data['name']))) {
        $errors['name'] = 'Поле должно быть заполнено';
    }

//    Валидация поля с названием проекта
    if (!empty($data['project'])) {
        $user_category = intval($data['project']);
        $category_valid = false;

        foreach ($categories as $category) {
            if ($user_category === $category['id']) {
                $category_valid = true;
                break;
            }
        }

        if ($category_valid === false) {
            $errors['project'] = 'Проект не существует';
        };
    }

//    Валидация поля с датой
    if (!empty($data['date'])) {

        if (strtotime($data['date']) < time()) {
            $errors['date'] = 'Дата должна быть больше или равна текущей';
        }
    }

    return $errors;
}


/**
 * Функция валидирует данные формы добавления пользователя
 * @param array $data - данные из формы
 * @param array $users - данные пользователей
 * @return array $errors - массив ошибок
 */
function validate_register_form ($data, $users)
{
    $errors = [];

//    Валидация поля E-mail
    if (empty($data['email'])) {
        $errors['email'] = 'Поле E-mail обязательное';
    } else if (!filter_var(($_POST['email']), FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'E-mail введён некорректно';
    } else {
        foreach ($users as $user) {
            if ($data['email'] === $user['email']) {
                $errors['email'] = 'E-mail уже занят';
                break;
            }
        }
    }

//    Валидация поля Пароль
    if (empty($data['password'])) {
        $errors['password'] = 'Поле Пароль обязательное';
    }

//    Валидация поля Имя
    if (empty($data['name'])) {
        $errors['name'] = 'Поле Имя обязательное';
    }

    return $errors;
}


/**
 * Функция добавляет задачу в БД
 * @param $con mysqli - ресурс соединения
 * @param $data array - данные для запроса
 * @return bool|int|string - id последнего запроса
 */
function db_add_task ($con, $data)
{
    $sql = 'INSERT INTO task (name, dt_due, file, category_id, user_id) 
            VALUES (?, ?, ?, ?, ?)';
    $stmt = db_get_prepare_stmt($con, $sql, $data);
    mysqli_stmt_execute($stmt);
    return mysqli_insert_id($con);
}

/**
 * Функция добавляет пользователя в БД
 * @param $con mysqli - ресурс соединения
 * @param $data array - данные для запроса
 * @return bool|int|string - id последнего запроса
 */
function db_add_user ($con, $data)
{
    $sql = 'INSERT INTO user (name, email, password) 
            VALUES (?, ?, ?)';
    $stmt = db_get_prepare_stmt($con, $sql, $data);
    mysqli_stmt_execute($stmt);
    return mysqli_insert_id($con);
}
