<?php
require '../config.php';
require '../mailer.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $selectedProducts = $_POST['products'] ?? [];

    if (empty($selectedProducts)) {
        die("Ошибка: Выберите хотя бы один товар.");
    }

    $totalQty = 0;
    $totalSum = 0;
    $orderItems = [];

    foreach ($selectedProducts as $productData) {
        list($productName, $price, $qty) = explode(',', $productData);
        $totalQty += $qty;
        $totalSum += $price * $qty;
        $orderItems[] = ['name_product' => $productName, 'price' => $price, 'qty' => $qty];
    }

    $token = bin2hex(random_bytes(16));

    $stmt = $pdo->prepare("INSERT INTO orders (name, email, qty, sum, currency, status, token, created_at) VALUES (?, ?, ?, ?, 'RUB', 'pending', ?, NOW())");
    $stmt->execute([$name, $email, $totalQty, $totalSum, $token]);
    $orderId = $pdo->lastInsertId();

    foreach ($orderItems as $product) {
        $stmt = $pdo->prepare("INSERT INTO order_items (id_order, name_product, price, qty) VALUES (?, ?, ?, ?)");
        $stmt->execute([$orderId, $product['name_product'], $product['price'], $product['qty']]);
    }

    $paypalUrl = $_ENV['PAYPAL_URL'] . urlencode($_ENV['PAYPAL_BUSINESS']) . "&amount=$totalSum&currency_code=RUB";

    $paypalUrl = $_ENV['PAYPAL_LINK'] . "?amount=$totalSum&currency_code=RUB";

    header("Location: " . $paypalUrl);
    exit();

    sendMail($email, "Подтверждение заказа", "Ваш заказ #$orderId ожидает оплаты. Перейдите по <a href='$paypalUrl'>ссылке</a>, чтобы оплатить.");
    sendMail($_ENV['MAIL_TO_ADDRESS'], "Новый заказ размещен", "Новый заказ #$orderId размещен. Покупатель: $name ($email). Сумма: $totalSum RUB.");
}
?>
