<?php
require 'config.php';
require 'mailer.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $products = $_POST['products'] ?? [];

    if (empty($products)) {
        die("Выберете хотя бы 1 продукт");
    }

    $totalQty = 0;
    $totalSum = 0;
    foreach ($products as $product) {
        list($productName, $price, $qty) = explode(',', $product);
        $totalQty += $qty;
        $totalSum += $price * $qty;
    }

    $token = bin2hex(random_bytes(16));

    $stmt = $pdo->prepare("INSERT INTO orders (name, email, qty, sum, currency, status, token, created_at) VALUES (?, ?, ?, ?, 'USD', 'pending', ?, NOW())");
    $stmt->execute([$name, $email, $totalQty, $totalSum, $token]);
    $orderId = $pdo->lastInsertId();

    foreach ($products as $product) {
        list($productName, $price, $qty) = explode(',', $product);
        $stmt = $pdo->prepare("INSERT INTO order_items (id_order, name_product, price, qty) VALUES (?, ?, ?, ?)");
        $stmt->execute([$orderId, $productName, $price, $qty]);
    }

    $paypalUrl = "";
}
?>
