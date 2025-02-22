<?php
require dirname(__DIR__) . '/config.php';
require '../api/paypal.php';
require dirname(__DIR__) . '/mailer.php';

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

$name = $order['name'];
$email = $order['email'];

$order_id = $order['id'];

sendMail($email, "Оплата отменена", "Ваш заказ #$order_id не был завершен. Если отмена произошла по ошибке, можете повторно перейти к оплате.");
sendMail($_ENV['MAIL_TO_ADDRESS'], "Заказ #$order_id отменен", "Заказ #$order_id от $name ($email) был отменен.");
ob_clean();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Оплата отменена</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-md text-center">
        <h2 class="text-2xl font-semibold mb-4 text-red-600">Оплата отменена</h2>
        <p class="mb-4 text-gray-700">Вы отменили оплату заказа №<?= htmlspecialchars($order_id) ?>.</p>
        <p class="mb-4 text-gray-600">Если это ошибка, попробуйте снова.</p>
        <a href="../order" class="mt-4 inline-block bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Разместить еще один заказ</a>
    </div>
</body>
</html>
