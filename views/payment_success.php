<?php
require dirname(__DIR__) . '/config.php';
require dirname(__DIR__) . '/mailer.php';
require dirname(__DIR__) . '/paypal.php';

if (!isset($_GET['orderID'])) {
    die("Ошибка: Order ID не найден.");
}

$orderID = $_GET['orderID'];
$token = $_GET['token'] ?? null;

$paypal = new PaypalCheckout();
$paymentDetails = $paypal->validate($orderID);

if (!$paymentDetails || empty($paymentDetails['status'])) {
    die("Ошибка: Не удалось получить данные о платеже.");
}

if ($paymentDetails['status'] !== 'COMPLETED') {
    die("Ошибка: Оплата не подтверждена.");
}

$stmt = $pdo->prepare("UPDATE orders SET status = 'paid', token = ? WHERE token = ?");
$stmt->execute([$orderID, $token]);

$stmt = $pdo->prepare("SELECT * FROM orders WHERE token = ?");
$stmt->execute([$orderID]);
$order = $stmt->fetch();

$name = $order['name'];
$email = $order['email'];

$order_id = $order['id'];

sendMail($email, "Оплата получена", "Благодарим за оплату! Ваш заказ #$order_id успешно оплачен.");
sendMail($_ENV['MAIL_TO_ADDRESS'], "Заказ #$order_id Оплачен", "Заказ #$order_id от $name ($email) успешно оплачен.");
ob_clean();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Оплата успешна</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-md text-center">
        <h2 class="text-2xl font-semibold mb-4 text-green-600">Оплата прошла успешно!</h2>
        <p class="mb-4 text-gray-700">Ваш заказ №<?= htmlspecialchars($order_id) ?> был успешно оплачен.</p>
        <p class="mb-4 text-gray-600">Статус: <span class="font-bold text-green-700"><?= htmlspecialchars($paymentDetails['status']) ?></span></p>
        <a href="../order" class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Сделать еще один заказ</a>
    </div>
</body>
</html>
