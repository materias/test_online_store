<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'config.php';

$request = trim($_SERVER['REQUEST_URI'], '/');

if (strpos($request, 'test_online_store') === 0) {
    $request = substr($request, strlen('test_online_store'));
}

$request = trim($request, '/');

if (empty($request)) {
    require 'pages/order.php';
    exit();
}

switch ($request) {
    case 'order':
        require 'pages/order.php';
        break;
    case 'orders':
        require 'pages/orders.php';
        break;
    case (preg_match('/^order_details\?id=\d+$/', $request) ? true : false):
        require 'pages/order_details.php';
        break;
    default:
        http_response_code(404);
        echo "404 - Страница не найдена.";
        break;
}
