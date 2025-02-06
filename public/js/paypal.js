document.addEventListener("DOMContentLoaded", function () {
    paypal.Buttons({
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: document.getElementById("orderTotal").value
                    }
                }]
            });
        },
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(details) {
                alert('Оплата прошла успешно, ' + details.payer.name.given_name);
            });
        },
        onCancel: function(data) {
            alert('Оплата отменена');
        }
    }).render('#paypal-button-container');
});
