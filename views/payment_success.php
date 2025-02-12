<?php
require '../config.php'; 
require '../mailer.php';
require '../api/paypal.php';

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

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Оплата успешна</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-md text-center">
        <h2 class="text-2xl font-semibold mb-4 text-green-600">Оплата прошла успешно!</h2>
        <p class="mb-4">Ваш заказ №<?= htmlspecialchars($orderID) ?> оплачен.</p>
        <p class="mb-4">Статус: <strong><?= htmlspecialchars($paymentDetails['status']) ?></strong></p>
        <a href="../order" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded">Сделать еще один заказ</a>
    </div>
</body>
</html>
