<?php
require '../config.php';

$routes = [
    '/' => 'views/checkout.php',
    '/order' => 'pages/order.php',
    '/orders' => 'pages/orders.php'
];

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
if (array_key_exists($path, $routes)) {
    require $routes[$path];
} else {
    http_response_code(404);
    echo "Page not found.";
}
