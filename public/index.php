<?php
require '../config.php';

$request = $_GET['route'] ?? '';

switch ($request) {
    case '':
        require '../views/checkout.php';
        break;
    case 'order':
        require '../pages/order.php';
        break;
    case 'orders':
        require '../pages/orders.php';
        break;
    case 'order_details':
        require '../pages/order_details.php';
        break;
    default:
        http_response_code(404);
        echo "404 - Page not found.";
        break;
}
