<?php
require dirname(__DIR__) . '/config.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Ошибка: отсутствует ID заказа!");
}

$orderId = (int) $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Ошибка: заказ не найден!");
}

$stmt = $pdo->prepare("SELECT * FROM order_items WHERE id_order = ?");
$stmt->execute([$orderId]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Детали заказа №<?= $order['id'] ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">

    <div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Заказ №<?= $order['id'] ?></h2>

        <div class="mb-6">
            <p><strong class="text-gray-600">Имя:</strong> <?= htmlspecialchars($order['name']) ?></p>
            <p><strong class="text-gray-600">Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
            <p><strong class="text-gray-600">Всего позиций:</strong> <?= $order['qty'] ?></p>
            <p><strong class="text-gray-600">Сумма заказа:</strong> <span class="font-bold">$<?= number_format($order['sum'], 2) ?> <?= $order['currency'] ?></span></p>
            <p><strong class="text-gray-600">Статус:</strong> 
                <span class="font-semibold 
                    <?= $order['status'] == 'paid' ? 'text-green-600' : ($order['status'] == 'cancelled' ? 'text-red-600' : 'text-gray-500') ?>">
                    <?= ucfirst($order['status']) ?>
                </span>
            </p>
            <p><strong class="text-gray-600">Заказ создан:</strong> <?= $order['created_at'] ?></p>
        </div>

        <h3 class="text-xl font-semibold text-gray-800 mb-3">Детали заказа</h3>
        
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-4 py-2 border">Продукт</th>
                        <th class="px-4 py-2 border">Цена</th>
                        <th class="px-4 py-2 border">Количество</th>
                        <th class="px-4 py-2 border">Сумма</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr class="border hover:bg-gray-100 transition">
                            <td class="px-4 py-2 border"><?= htmlspecialchars($item['name_product']) ?></td>
                            <td class="px-4 py-2 border">$<?= number_format($item['price'], 2) ?></td>
                            <td class="px-4 py-2 border"><?= $item['qty'] ?></td>
                            <td class="px-4 py-2 border font-semibold">$<?= number_format($item['price'] * $item['qty'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-6 text-center">
            <a href="/test_online_store/orders" class="bg-blue-500 text-white px-4 py-2 rounded shadow-md hover:bg-blue-600 transition">Назад к заказам</a>
        </div>
    </div>

</body>
</html>
