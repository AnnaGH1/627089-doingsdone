<?php

require_once 'vendor/autoload.php';
require_once 'connection.php';
require_once 'functions.php';

$host_name = 'smtp.phpdemo.ru';
$from_name = 'Keks';
$from_email = 'keks@phpdemo.ru';
$from_password = 'htmlacademy';
$tasks_notify = get_tasks_notify($con, []);

// Transport config
$transport = new Swift_SmtpTransport($host_name, 25);
$transport->setUsername($from_email);
$transport->setPassword($from_password);

// Message generation
$message = new Swift_Message('Notification from "Doingsdone"');
$message->setFrom([$from_email => 'Doingsdone']);

// Message sending
$mailer = new Swift_Mailer($transport);

foreach ($tasks_notify as $task_notify) {
    $message->setTo([$task_notify['email'] => $task_notify['user_name']]);
    $message->setBody(
        'Dear ' . htmlspecialchars($task_notify['user_name']) . '!<br>' . 'You have tasks ' . htmlspecialchars($task_notify['tasks']) . ' due within an hour.',
        'text/html'
    );

    $result = $mailer->send($message);

    if ($result) {
        print('Notification is successfully sent to ' . htmlspecialchars($task_notify['email']) . '<br>');
    } else {
        print('Failed to send notification to ' . htmlspecialchars($task_notify['email']) . '<br>');
    }
}

