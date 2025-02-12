<?php
require dirname(__DIR__) . '/config.php';
require dirname(__DIR__) . '/mailer.php';

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

    echo json_encode(["status" => "success", "payment_url" => "/test_online_store/pages/process_order.php?token=$token"]);
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

$name = $order['name'];
$email = $order['email'];
$total_sum = $order['sum'];
$paypalUrl = "https://www.paypal.com/checkoutnow?token=$token";

$order_id = $order['id'];

sendMail($email, "Подтверждение заказа", "Ваш заказ #$order_id ожидает оплаты. Перейдите по <a href='$paypalUrl'>ссылке</a>, чтобы оплатить.");
sendMail($_ENV['MAIL_TO_ADDRESS'], "Новый заказ размещен", "Новый заказ #$order_id размещен. Покупатель: $name ($email). Сумма: USD $total_sum.");
ob_clean();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оплата заказа</title>
    <script src="https://www.paypal.com/sdk/js?client-id=<?= $clientId ?>&currency=<?= $currency ?>"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen">
    <div class="bg-white shadow-lg rounded-lg p-8 max-w-md w-full text-center">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Оплатите заказ №<?= $order_id ?></h2>
        <p class="text-lg text-gray-700 mb-6">Сумма к оплате: <span class="font-bold text-gray-900">$<?= number_format($total_sum, 2) ?> <?= $currency ?></span></p>
        <div id="paypal-button-container" class="mt-4"></div>
    </div>

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
                        window.location.href = "test_online_store/views/payment_success.php?token=<?= $token ?>&orderID=" + details.id;
                    });
                },
                onCancel: function(data) {
                    window.location.href = "test_online_store/views/payment_cancel.php?token=<?= $token ?>";
                }
            }).render('#paypal-button-container');
        }

        document.addEventListener("DOMContentLoaded", renderPayPalButton);
    </script>
</body>
</html>
