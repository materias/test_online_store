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

    $paypalUrl = $_ENV['PAYPAL_URL'] . urlencode($businessEmail) . "&amount=$orderTotal&currency_code=RUB";

    sendMail($email, "Подтверждение заказа", "Ваш заказ #$orderId ожидает оплаты. Перейдите по <a href='$paypalUrl'>ссылке</a>, чтобы оплатить.");
    sendMail($_ENV['MAIL_FROM_ADDRES'], "Новый заказ размещен", "Новый заказ #$orderId размещен. Покупатель: $name ($email). Сумма: $$totalSum.");

    echo "Заказ успешно размещен! <a href='$paypalUrl'>Нажмите, чтобы оплатить</a>";

}
?>
