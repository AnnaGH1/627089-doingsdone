<?php

require_once 'vendor/autoload.php';
require_once 'connection.php';
require_once 'functions.php';

$host_name = 'smtp.phpdemo.ru';
$from_name = 'Кекс';
$from_email = 'keks@phpdemo.ru';
$from_password = 'htmlacademy';
$tasks_notify = get_tasks_notify($con, []);

// Конфигурация транспорта
$transport = new Swift_SmtpTransport($host_name, 25);
$transport->setUsername($from_email);
$transport->setPassword($from_password);

// Формирование сообщения
$message = new Swift_Message('Уведомление от сервиса "Дела в порядке"');
$message->setFrom([$from_email => 'Дела в порядке']);

// Отправка сообщения
$mailer = new Swift_Mailer($transport);

foreach ($tasks_notify as $task_notify) {
    $message->setTo([$task_notify['email'] => $task_notify['user_name']]);
    $message->setBody(
        'Уважаемый ' . htmlspecialchars($task_notify['user_name']) . '!<br>' . 'У вас запланированы задачи ' . htmlspecialchars($task_notify['tasks']) . ' на ближайший час.',
        'text/html'
    );

    $result = $mailer->send($message);

    if ($result) {
        print('Рассылка успешно отправлена по адресу ' . htmlspecialchars($task_notify['email']) . '<br>');
    } else {
        print('Не удалось отправить рассылку по адресу ' . htmlspecialchars($task_notify['email']) . '<br>');
    }
}

