<?php
require '../config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['orderID']) || !isset($data['payerID']) || !isset($data['paymentID'])) {
    echo json_encode(['success' => false, 'message' => 'Ошибка в данных PayPal']);
    exit;
}

$stmt = $pdo->prepare("UPDATE orders SET status = 'paid', token = ? WHERE id = 1"); 
$stmt->execute([$data['orderID']]);

require 'mailer.php';
sendMail($order['email'], 'Ваш заказ успешно оплачен!', 'Спасибо за ваш заказ.');

echo json_encode(['success' => true]);
?>
