<?php

require '../config.php';
require '../mailer.php';

if (!isset($_GET['token'])) {
    die("Ошибка: Токен заказа не найден.");
}

$token = $_GET['token'];

$stmt = $pdo->prepare("SELECT * FROM orders WHERE token = ?");
$stmt->execute([$token]);
$order = $stmt->fetch();

if (!$order) {
    die("Ошибка: Заказ не найден.");
}

$stmt = $pdo->prepare("UPDATE orders SET status = 'cancelled' WHERE token = ?");
$stmt->execute([$token]);

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Оплата отменена</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-md text-center">
        <h2 class="text-2xl font-semibold mb-4 text-red-600">Оплата отменена</h2>
        <p class="mb-4">Вы отменили оплату заказа №<?= $order['id'] ?>.</p>
        <p>Если это ошибка, попробуйте снова.</p>
        <a href="../pages/order.php" class="mt-4 bg-gray-500 text-white px-4 py-2 rounded">Вернуться в магазин</a>
    </div>
</body>
</html>
