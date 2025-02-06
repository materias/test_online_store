<?php
require '../config.php';
require '../mailer.php';

$jsonData = file_get_contents('data/orders.json');
$productsData = json_decode($jsonData, true);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];

    if (empty($productsData)) {
        die("Ошибка: данные о товарах отсутствуют.");
    }

    $totalQty = 0;
    $totalSum = 0;

    foreach ($productsData as $product) {
        $totalQty += $product['qty'];
        $totalSum += $product['price'] * $product['qty'];
    }

    $token = bin2hex(random_bytes(16));

    $stmt = $pdo->prepare("INSERT INTO orders (name, email, qty, sum, currency, status, token, created_at) VALUES (?, ?, ?, ?, 'RUB', 'pending', ?, NOW())");
    $stmt->execute([$name, $email, $totalQty, $totalSum, $token]);
    $orderId = $pdo->lastInsertId();

    foreach ($productsData as $product) {
        $stmt = $pdo->prepare("INSERT INTO order_items (id_order, name_product, price, qty) VALUES (?, ?, ?, ?)");
        $stmt->execute([$orderId, $product['name_product'], $product['price'], $product['qty']]);
    }

    $paypalUrl = $_ENV['PAYPAL_URL'] . urlencode($_ENV['PAYPAL_BUSINESS']) . "&amount=$totalSum&currency_code=RUB";

    sendMail($email, "Подтверждение заказа", "Ваш заказ #$orderId ожидает оплаты. Перейдите по <a href='$paypalUrl'>ссылке</a>, чтобы оплатить.");

    sendMail($_ENV['MAIL_FROM_ADDRESS'], "Новый заказ размещен", "Новый заказ #$orderId размещен. Покупатель: $name ($email). Сумма: $totalSum RUB.");

    echo "Заказ успешно размещен! <a href='$paypalUrl'>Нажмите, чтобы оплатить</a>";
}
?>

