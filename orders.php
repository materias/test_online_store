<?php
require 'config.php';

$sql = "SELECT * FROM orders ORDER BY created_at DESC";
$result = $pdo->query($sql);
$orders = $result->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Orders</title>
    </head>
    <body>

        <h2>Orders List</h2>
        
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Qty</th>
                <th>Sum ($)</th>
                <th>Currency</th>
                <th>Status</th>
                <th>Created At</th>
            </tr>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= $order['id'] ?></td>
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
