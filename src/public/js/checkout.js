document.addEventListener('DOMContentLoaded', () => {
    const stripe = Stripe(window.stripePublicKey); // グローバル変数に変更
    const elements = stripe.elements();
    const card = elements.create('card');
    card.mount('#card-element');

    const paymentForm = document.getElementById('payment-form');
    paymentForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const csrfToken = document.querySelector('#csrf-token').value;
        const amount = document.querySelector('#amount').value;

        try {
            const { paymentMethod, error } = await stripe.createPaymentMethod({
                type: 'card',
                card: card,
            });

            if (error) {
                document.querySelector('#payment-result').innerText = error.message;
                return;
            }

            const response = await fetch('/checkout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    payment_method: paymentMethod.id,
                    amount: amount,
                }),
            });

            const result = await response.json();

            if (result.success) {
                document.querySelector('#payment-result').innerText = '支払いが成功しました！';
            } else {
                document.querySelector('#payment-result').innerText = result.error;
            }
        } catch (err) {
            document.querySelector('#payment-result').innerText = 'エラーが発生しました。';
        }
    });
});
