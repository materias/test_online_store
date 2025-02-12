<?php
require '../config.php';

$clientId = $_ENV['CLIENT_ID'];
$clientSecret = $_ENV['CLIENT_SECRET'];
$currency = $_ENV['CURRENCY'] ?? 'USD';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $order_items = json_decode($_POST['order_items'], true);

    if (empty($name) || empty($email) || empty($order_items)) {
        die("Ошибка: Заполните все поля!");
    }

    $total_qty = 0;
    $total_sum = 0;

    foreach ($order_items as $item) {
        $total_qty += $item['qty'];
        $total_sum += $item['price'] * $item['qty'];
    }

    $token = bin2hex(random_bytes(16));
    $status = "pending";

    $stmt = $pdo->prepare("INSERT INTO orders (name, email, qty, sum, currency, status, token, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$name, $email, $total_qty, $total_sum, $currency, $status, $token]);

    $order_id = $pdo->lastInsertId();

    foreach ($order_items as $item) {
        $stmt = $pdo->prepare("INSERT INTO order_items (id_order, name_product, price, qty) VALUES (?, ?, ?, ?)");
        $stmt->execute([$order_id, $item['name_product'], $item['price'], $item['qty']]);
    }

    echo json_encode(["status" => "success", "payment_url" => "process_order.php?token=$token"]);
    exit();

}

if (!isset($_GET['token'])) {
    die("Ошибка: Токен не найден.");
}

$token = $_GET['token'];

$stmt = $pdo->prepare("SELECT * FROM orders WHERE token = ?");
$stmt->execute([$token]);
$order = $stmt->fetch();

if (!$order) {
    die("Ошибка: Заказ не найден.");
}

$order_id = $order['id'];
$total_sum = $order['sum'];

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Оплата заказа</title>
    <script src="https://www.paypal.com/sdk/js?client-id=<?= $clientId ?>&currency=<?= $currency ?>"></script>
    <script>
        function renderPayPalButton() {
            paypal.Buttons({
                createOrder: function(data, actions) {
                    return actions.order.create({
                        purchase_units: [{
                            amount: {
                                currency_code: "<?= $currency ?>",
                                value: "<?= number_format($total_sum, 2) ?>"
                            }
                        }]
                    });
                },
                onApprove: function(data, actions) {
                    return actions.order.capture().then(function(details) {
                        window.location.href = "../views/payment_success.php?token=<?= $token ?>&orderID=" + details.id;
                    });
                },
                onCancel: function(data) {
                    window.location.href = "../views/payment_cancel.php?token=<?= $token ?>";
                }
            }).render('#paypal-button-container');
        }

        document.addEventListener("DOMContentLoaded", renderPayPalButton);
    </script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-md text-center">
        <h2 class="text-2xl font-semibold mb-4">Оплатите заказ №<?= $order_id ?></h2>
        <p class="mb-4">Сумма к оплате: <strong>$<?= number_format($total_sum, 2) ?> <?= $currency ?></strong></p>
        <div id="paypal-button-container"></div>
    </div>
</body>
</html>
