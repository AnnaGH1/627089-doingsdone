<?php

require_once 'vendor/autoload.php';
require_once 'connection.php';
require_once 'functions.php';

$user_name = 'Кекс';
$user_email = 'keks@phpdemo.ru';

// Конфигурация транспорта
$transport = new Swift_SmtpTransport('phpdemo.ru', 25);
$transport->setUsername('keks@phpdemo.ru');
$transport->setPassword('htmlacademy');

// Отправка сообщения
$mailer = new Swift_Mailer($transport);


$tasks_notify = get_tasks_notify($con, []);
$task_names = [];

// Объединяет задачи в список
if (!empty($tasks_notify)) {
    foreach ($tasks_notify as $task_notify) {
        $task_names[] = $task_notify['name'];
    }
    $task_list = implode(', ', $task_names);
} else {
    $task_list = '';
}


// Формирование сообщения
$message = new Swift_Message('Уведомление от сервиса "Дела в порядке"');
$message->setFrom(['keks@phpdemo.ru' => 'Дела в порядке']);
$message->setTo([$user_email => $user_name]);
$message->setBody('Уважаемый ' . $user_name .'! У вас запланирована задача ' . $task_list . ' на ближайший час', 'text/html');

$result = $mailer->send($message);


if ($result) {
    print('Рассылка успешно отправлена');
} else {
    print('Не удалось отправить рассылку');
}

