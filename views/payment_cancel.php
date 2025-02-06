<?php
require 'config.php';
require 'mailer.php';

if (!isset($_GET['custom'])) {
    die("Неизвестный запрос");
}

$token = $_GET['custom'];

$stmt = $pdo->prepare("SELECT * FROM orders WHERE token = ?");
$stmt->execute([$token]);
$order = $stmt->fetch();

if (!$order) {
    die("Заказ не найден.");
}

$stmt = $pdo->prepare("UPDATE orders SET status = 'canceled' WHERE token = ?");
$stmt->execute([$token]);

sendMail($order['email'], "Оплата отменена", "Ваш заказ #{$order['id']} не был завершен. Если отмена произошла по ошибке, можете повторно перейти к оплате.");

sendMail($_ENV['MAIL_TO_ADDRES'], "Заказ #{$order['id']} отменен", "Заказ #{$order['id']} от {$order['name']} ({$order['email']}) был отменен.");

echo "Оплата отменена.";
?>
