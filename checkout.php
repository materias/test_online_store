<script src="<?= $_ENV['PAYPAL_LINK']; ?>"></script>

<div id="paypal-button-container"></div>

<script>
  paypal.Buttons({
    createOrder: function(data, actions) {
      return actions.order.create({
        purchase_units: [{
          amount: {
            value: '377.5'
          }
        }]
      });
    },
    onApprove: function(data, actions) {
      return actions.order.capture().then(function(details) {
        fetch('process_payment.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            orderID: data.orderID,
            payerID: data.payerID,
            paymentID: details.id
          })
        }).then(response => response.json()).then(data => {
          if (data.success) {
            alert('Оплата прошла успешно! Спасибо, ' + details.payer.name.given_name);
            window.location.href = 'success.php';
          } else {
            alert('Ошибка при обработке платежа.');
          }
        });
      });
    },
    onCancel: function(data) {
      alert('Оплата отменена');
    }
  }).render('#paypal-button-container');
</script>
