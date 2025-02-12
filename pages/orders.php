<?php
require dirname(__DIR__) . '/config.php';

$sql = "SELECT * FROM orders ORDER BY created_at DESC";
$result = $pdo->query($sql);
$orders = $result->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список заказов</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-5xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold mb-4 text-gray-800">Список заказов</h2>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-4 py-2 border">ID заказа</th>
                        <th class="px-4 py-2 border">Имя</th>
                        <th class="px-4 py-2 border">Email</th>
                        <th class="px-4 py-2 border">Количество</th>
                        <th class="px-4 py-2 border">Сумма</th>
                        <th class="px-4 py-2 border">Валюта</th>
                        <th class="px-4 py-2 border">Статус</th>
                        <th class="px-4 py-2 border">Дата заказа</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr class="border hover:bg-gray-100 transition">
                            <td class="px-4 py-2 border">
                                <a href="pages/order_details.php?id=<?= $order['id'] ?>" class="text-blue-600 hover:underline"><?= $order['id'] ?></a>
                            </td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($order['name']) ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($order['email']) ?></td>
                            <td class="px-4 py-2 border"><?= $order['qty'] ?></td>
                            <td class="px-4 py-2 border">$<?= number_format($order['sum'], 2) ?></td>
                            <td class="px-4 py-2 border"><?= $order['currency'] ?></td>
                            <td class="px-4 py-2 border font-semibold 
                                <?= $order['status'] == 'paid' ? 'text-green-600' : ($order['status'] == 'cancelled' ? 'text-red-600' : 'text-gray-500') ?>">
                                <?= ucfirst($order['status']) ?>
                            </td>
                            <td class="px-4 py-2 border"><?= $order['created_at'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
