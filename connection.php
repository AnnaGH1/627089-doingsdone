<?php
$con = mysqli_connect('localhost', 'root', '', '627089-doingsdone');

if ($con === false) {
print('Ошибка подключения: ' . mysqli_connect_error());
exit;
}

mysqli_set_charset($con, 'utf8');
