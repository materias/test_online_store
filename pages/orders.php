<?php
require dirname(__DIR__) . '/config.php';

$sql = "SELECT * FROM orders ORDER BY created_at DESC";
$result = $pdo->query($sql);
$orders = $result->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Заказы</title>
        <link rel="stylesheet" href="../public/css/styles.css">
    </head>
    <body>

        <h2>Список заказов</h2>
        
        <table>
            <tr>
                <th>ID заказа</th>
                <th>Имя</th>
                <th>Email</th>
                <th>Количество</th>
                <th>Сумма</th>
                <th>Валюта</th>
                <th>Статус</th>
                <th>Заказ создан</th>
            </tr>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td><a href="/test_online_store/order_details?id=<?= $order['id'] ?>"><?= $order['id'] ?></a></td>
                <td><?= htmlspecialchars($order['name']) ?></td>
                <td><?= htmlspecialchars($order['email']) ?></td>
                <td><?= $order['qty'] ?></td>
                <td><?= number_format($order['sum'], 2) ?></td>
                <td><?= $order['currency'] ?></td>
                <td class="<?= $order['status'] == 'paid' ? 'paid' : 'canceled' ?>">
                    <?= ucfirst($order['status']) ?>
                </td>
                <td><?= $order['created_at'] ?></td>
            </tr>
            <?php endforeach; ?>
        </table>

    </body>
</html>
