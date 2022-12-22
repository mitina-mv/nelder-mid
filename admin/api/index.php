<?php
header('Access-Control-Allow-Methods: GET, POST, PUT');

include_once($_SERVER['DOCUMENT_ROOT'] . '/admin/init.php');
include_once 'helpers/query.php';


// Получаем данные из запроса
$data = \Helpers\query\getRequestData();
$router = $data['router'];

// Проверяем роутер на валидность
if (\Helpers\query\isValidRouter($router)) {

    // Подключаем файл-роутер
    include_once "routers/$router.php";

    // Запускаем главную функцию
    route($data);
} else {
    // Выбрасываем ошибку
    \Helpers\query\throwHttpError('invalid_router', 'router not found');
}
