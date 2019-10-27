<?php
/**
 * Creates a prepared statement from an SQL query and data
 *
 * @param mysqli $link - link identifier
 * @param string $sql - SQL query with placeholders
 * @param array $data - data to replace placeholders
 *
 * @return mysqli_stmt - prepared statement
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
 * Counts tasks by category
 * @param array $task_list - tasks, each task is an associative array that has a 'category_name' key
 * @param string $task_category - value corresponding to a 'category_name' key
 * @return int $counter - tasks count
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
 * Checks the task due date, if due within 24h, returns true
 * @param array $task - task (associative array)
 * @return boolean $status - task status
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
 * Includes a template
 * @param string $name - name of the file in 'templates' folder
 * @param array $data - associative array, contains variables for a given template, a key and a variable name match
 * @return string $result - empty string if a template does not exist or html code
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
 * Gets users
 * @param mysqli $con - connection
 * @return array - users associative array or an empty array
 */
function get_users($con)
{
    $sql = 'SELECT * FROM user';
    $stmt = db_get_prepare_stmt($con, $sql);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result === false) {
        return [];
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Gets categories
 * @param mysqli $con - connection
 * @param array $data - query data - user id
 * @return array - categories associative array or an empty array
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
 * Gets tasks and their categories for a user
 * @param mysqli $con - connection
 * @param array $data - query data - user id
 * @return array - tasks associative array or an empty array
 */
function get_tasks($con, $data)
{
    $sql = 'SELECT task.*, DATE_FORMAT(task.dt_due, "%b %d %Y") AS due FROM task 
            WHERE user_id = ? ORDER BY task.dt_add DESC';

    $stmt = db_get_prepare_stmt($con, $sql, $data);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result === false) {
        return [];
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Gets tasks in a selected category for a user
 * @param mysqli $con - connection
 * @param array $data - query data - user id and category id
 * @return array - tasks associative array or an empty array
 */
function get_tasks_by_category($con, $data)
{
    $sql = 'SELECT task.*, category.name AS category_name, DATE_FORMAT(task.dt_due, "%d.%m.%Y") AS due FROM task 
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
 * Gets tasks with a selected due date for a user
 * @param mysqli $con - connection
 * @param array $data - query data - user id and task due date
 * @return array - tasks associative array or an empty array
 */
function get_tasks_by_due_date($con, $data)
{
    $sql = 'SELECT task.*, DATE_FORMAT(task.dt_due, "%b %d %Y") AS due FROM task
            JOIN user ON user.id = task.user_id WHERE task.user_id = ? AND dt_due = ?';
    $stmt = db_get_prepare_stmt($con, $sql, $data);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result === false) {
        return [];
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Gets tasks overdue for a user
 * @param mysqli $con - connection
 * @param array $data - query data - user id
 * @return array - tasks associative array or an empty array
 */
function get_tasks_overdue($con, $data)
{
    $sql = 'SELECT task.*, DATE_FORMAT(task.dt_due, "%b %d %Y") AS due FROM task
            JOIN user ON user.id = task.user_id WHERE task.user_id = ? AND task.dt_due < CURDATE()';
    $stmt = db_get_prepare_stmt($con, $sql, $data);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result === false) {
        return [];
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Gets tasks queried by a user
 * @param mysqli $con - connection
 * @param array $data - query data - query and user id
 * @return array - tasks associative array or an empty array
 */
function get_tasks_by_query ($con, $data)
{
    $sql = 'SELECT *, DATE_FORMAT(task.dt_due, "%d.%m.%Y") AS due FROM task 
            WHERE MATCH(task.name) AGAINST (?) AND task.user_id = ?';
    $stmt = db_get_prepare_stmt($con, $sql, $data);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result === false) {
        return [];
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Gets tasks that are not completed and due within an hour
 * @param mysqli $con - connection
 * @param array $data - query data
 * @return array - tasks associative array or an empty array
 */
function get_tasks_notify ($con, $data)
{
    $sql = 'SELECT GROUP_CONCAT(task.name SEPARATOR ", ") tasks, user.email, user.name AS user_name 
            FROM task
            JOIN user ON user.id = task.user_id
            WHERE task.dt_complete IS NULL
            AND UNIX_TIMESTAMP(task.dt_due) - UNIX_TIMESTAMP() <= 3600
            GROUP BY user.email';
    $stmt = db_get_prepare_stmt($con, $sql, $data);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result === false) {
        return [];
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Generates query string that contains a 'category_id' key
 * @param string $category_id - query string value
 * @return string $query - query string
 */
function get_category_url($category_id)
{
    $params = $_GET;
    $params['category_id'] = $category_id;
    $query = http_build_query($params);
    return $query;
}

/**
 * Validates a new task form
 * @param array $data - form data
 * @param array $categories - tasks categories
 * @return array $errors - errors
 */
function validate_task_form ($data, $categories)
{
    $errors = [];

    // Task
    if (empty(trim($data['name']))) {
        $errors['name'] = 'Title is required';
    }

    // Project
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
            $errors['project'] = 'Project does not exist';
        };
    }

    // Date
    if (!empty($data['date'])) {

        if ((strtotime($data['date']) + 60 * 60 * 24 - 1) - time () <= 0) {
            $errors['date'] = 'Date must be the current date or later';
        }
    }
    return $errors;
}

/**
 * Validates a new project form
 * @param array $data  - form data
 * @param $categories  - tasks categories
 * @return array $errors - errors
 */
function validate_category_form ($data, $categories)
{
    $errors = [];
    if (empty(trim($data['name']))) {
        $errors['name'] = 'Title is required';
    }

    // Project
    if (!empty($data['name'])) {
        $user_category = $data['name'];

        foreach ($categories as $category) {
            if ($user_category === $category['name']) {
                $errors['name'] = 'Project already exists';
                break;
            }
        }
    }

    return $errors;
}

/**
 * Validates a user registration form
 * @param array $data - form data
 * @param array $users - users data
 * @return array $errors - errors
 */
function validate_register_form ($data, $users)
{
    $errors = [];

    // E-mail
    if (empty($data['email'])) {
        $errors['email'] = 'E-mail is required';
    } else if (!filter_var(($_POST['email']), FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'E-mail is incorrect';
    } else {
        foreach ($users as $user) {
            if ($data['email'] === $user['email']) {
                $errors['email'] = 'E-mail is already taken';
                break;
            }
        }
    }

    // Password
    if (empty($data['password'])) {
        $errors['password'] = 'Password is required';
    }

    // Name
    if (empty($data['name'])) {
        $errors['name'] = 'Name is required';
    }

    return $errors;
}

/**
 * Validates a user authentication form
 * @param array $data - form data
 * @return array $errors - errors
 */
function validate_auth_form ($data)
{
    $errors = [];

    // E-mail
    if (empty($data['email'])) {
        $errors['email'] = 'E-mail is required';
    } else if (!filter_var(($data['email']), FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'E-mail is incorrect';
    }

    // Password
    if (empty($data['password'])) {
        $errors['password'] = 'Password is required';
    }

    return $errors;
}

/**
 * Checks if a current user is an authenticated user
 * @return bool
 */
function isAuth ()
{
    return isset($_SESSION['id']);
}

/**
 * Adds a task to a database
 * @param $con mysqli - connection
 * @param $data array - query data
 * @return bool|int|string - last query id
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
 * Adds a user to a database
 * @param $con mysqli - connection
 * @param $data array - query data
 * @return bool|int|string - last query id
 */
function db_add_user ($con, $data)
{
    $sql = 'INSERT INTO user (name, email, password) 
            VALUES (?, ?, ?)';
    $stmt = db_get_prepare_stmt($con, $sql, $data);
    mysqli_stmt_execute($stmt);
    return mysqli_insert_id($con);
}

/**
 * Adds a category to a database
 * @param $con mysqli - connection
 * @param $data array - query data
 * @return bool|int|string - last query id
 */
function db_add_category ($con, $data)
{
    $sql = 'INSERT INTO category (name, user_id) 
            VALUES (?, ?)';
    $stmt = db_get_prepare_stmt($con, $sql, $data);
    mysqli_stmt_execute($stmt);
    return mysqli_insert_id($con);
}

/**
 * Sets task status to completed
 * @param $con mysqli - connection
 * @param $data array - query data
 * @return int|string
 */
function db_add_dt_complete ($con, $data)
{
    $sql = 'UPDATE task SET dt_complete = NOW() WHERE id = ?';
    $stmt = db_get_prepare_stmt($con, $sql, $data);
    mysqli_stmt_execute($stmt);
    return mysqli_insert_id($con);
}

/**
 * Sets task status to not completed
 * @param $con mysqli - connection
 * @param $data array - query data
 * @return int|string
 */
function db_remove_dt_complete ($con, $data)
{
    $sql = 'UPDATE task SET dt_complete = null WHERE id = ?';
    $stmt = db_get_prepare_stmt($con, $sql, $data);
    mysqli_stmt_execute($stmt);
    return mysqli_insert_id($con);
}
