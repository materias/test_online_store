<?php
require 'config.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Order ID is required.");
}

$order_id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Заказ не найден.");
}

$stmt = $pdo->prepare("SELECT * FROM order_items WHERE id_order = ?");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Детали заказа</title>
</head>
<body>

    <h2>Заказ №<?= $order['id'] ?></h2>
    <p><strong>Имя:</strong> <?= htmlspecialchars($order['name']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
    <p><strong>Всего позиций:</strong> <?= $order['qty'] ?></p>
    <p><strong>Сумма заказа:</strong> $<?= number_format($order['sum'], 2) ?></p>
    <p><strong>Валюта:</strong> <?= $order['currency'] ?></p>
    <p><strong>Статус:</strong> <span class="<?= $order['status'] == 'paid' ? 'paid' : 'canceled' ?>">
        <?= ucfirst($order['status']) ?></span>
    </p>
    <p><strong>Заказ создан:</strong> <?= $order['created_at'] ?></p>

    <h3>Детали заказа</h3>
    <table>
        <tr>
            <th>Продукт</th>
            <th>Цена</th>
            <th>Количество</th>
            <th>Сумма</th>
        </tr>
        <?php foreach ($items as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['name_product']) ?></td>
            <td><?= number_format($item['price'], 2) ?></td>
            <td><?= $item['qty'] ?></td>
            <td><?= number_format($item['price'] * $item['qty'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <p><a href="orders.php">Назад в Заказы</a></p>

</body>
</html>
