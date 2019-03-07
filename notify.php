<?php

require_once 'vendor/autoload.php';
require_once 'connection.php';
require_once 'functions.php';

$user_id = 7;
$tasks_notify = get_tasks_notify($con, [$user_id]);
$task_names = [];

$host_name = 'smtp.phpdemo.ru';

$from_name = 'Кекс';
$from_email = 'keks@phpdemo.ru';
$from_password = 'htmlacademy';
$task_list = '';

if (count($tasks_notify) === 0) {
    print 'Нет задач для уведомления';
    exit;
}

$user_name = $tasks_notify[0]['user_name'];
$user_email = $tasks_notify[0]['email'];

// Конфигурация транспорта
$transport = new Swift_SmtpTransport($host_name, 25);
$transport->setUsername($from_email);
$transport->setPassword($from_password);

// Формирование сообщения
$message = new Swift_Message('Уведомление от сервиса "Дела в порядке"');
$message->setFrom([$from_email => 'Дела в порядке']);
$message->setTo([$user_email => $user_name]);
$message->setBody('Уважаемый ' . $user_name .' ! У вас запланирована задача ' . $task_list . ' на ближайший час', 'text/html');


// Объединяет задачи в список
if (!empty($tasks_notify)) {
    foreach ($tasks_notify as $task_notify) {
        $task_names[] = $task_notify['name'];
    }
    $task_list = implode(', ', $task_names);
} else {
    $task_list = '';
}


// Отправка сообщения
$mailer = new Swift_Mailer($transport);
$result = $mailer->send($message);


if ($result) {
    print('Рассылка успешно отправлена');
} else {
    print('Не удалось отправить рассылку');
}

