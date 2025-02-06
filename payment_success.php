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

$stmt = $pdo->prepare("UPDATE orders SET status = 'paid' WHERE token = ?");
$stmt->execute([$token]);

sendMail($order['email'], "Оплата получена", "Благодарим за оплату! Ваш заказ #{$order['id']} успешно оплачен.");

sendMail($_ENV['MAIL_TO_ADDRES'], "Заказ #{$order['id']} Оплачен", "Заказ #{$order['id']} от {$order['name']} ({$order['email']}) успешно оплачен.");

echo "Спасибо! Оплата прошла успешно";
?>
