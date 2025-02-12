<?php
$products_json = file_get_contents('../data/orders.json');
$products = json_decode($products_json, true);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заказ</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold mb-4">Оформление заказа</h2>
        <form id="orderForm">
            <label for="name" class="block text-sm font-medium text-gray-700">Имя:</label>
            <input type="text" id="name" name="name" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            <br>

            <label for="email" class="block text-sm font-medium text-gray-700">Email:</label>
            <input type="email" id="email" name="email" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            <br>

            <h3 class="text-xl font-bold mt-4">Выберите товары:</h3>
            <div class="grid gap-4">
                <?php foreach ($products as $product): ?>
                    <label class="flex items-center gap-4 p-4 border rounded-lg shadow-md bg-white">
                        <input type="checkbox" name="products[]" value="<?= htmlspecialchars(json_encode($product)) ?>" class="w-6 h-6 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <div class="flex flex-col">
                            <span class="font-bold text-lg"><?= htmlspecialchars($product['name_product']) ?></span>
                            <div class="flex gap-2">
                                <span class="text-gray-500 w-28">Цена:</span>
                                <span class="text-gray-900">USD <?= number_format($product['price'], 2) ?></span>
                            </div>
                            <div class="flex gap-2">
                                <span class="text-gray-500 w-28">Кол-во:</span>
                                <span class="text-gray-900"><?= $product['qty'] ?></span>
                            </div>
                        </div>
                    </label>
                <?php endforeach; ?>
            </div>
            <br>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700">Оплатить</button>
        </form>
    </div>

    <script>
        document.getElementById("orderForm").addEventListener("submit", async function(e) {
            e.preventDefault();

            let formData = new FormData();
            formData.append("name", document.getElementById("name").value);
            formData.append("email", document.getElementById("email").value);

            let selectedProducts = [];
            document.querySelectorAll("input[name='products[]']:checked").forEach((checkbox) => {
                selectedProducts.push(JSON.parse(checkbox.value));
            });

            if (selectedProducts.length === 0) {
                alert("Выберите хотя бы один товар!");
                return;
            }

            formData.append("order_items", JSON.stringify(selectedProducts));

            let response = await fetch("process_order.php", {
                method: "POST",
                body: formData
            });

            console.log('here')

            let result = await response.json();
            if (result.status === "success") {
                window.location.href = result.payment_url;
            } else {
                alert(result.message);
            }
        });
    </script>
</body>
</html>
