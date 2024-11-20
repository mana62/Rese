document.addEventListener("DOMContentLoaded", function () {
    if (!window.stripePublicKey) {
        console.error("Stripe public key is not defined.");
        return;
    }

    const stripe = Stripe(window.stripePublicKey);
    const elements = stripe.elements();
    const cardElement = elements.create("card");

    // カードフォームの表示
    cardElement.mount("#card-element");

    const form = document.getElementById("payment-form");
    form.addEventListener("submit", async (event) => {
        event.preventDefault();

        document.getElementById("submit-button").disabled = true;

        const { paymentMethod, error } = await stripe.createPaymentMethod({
            type: "card",
            card: cardElement,
        });

        if (error) {
            document.getElementById("payment-result").textContent = error.message;
            document.getElementById("submit-button").disabled = false;
        } else {
            const response = await fetch("/process-payment", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.getElementById("csrf-token").value,
                },
                body: JSON.stringify({
                    payment_method: paymentMethod.id,
                    amount: document.getElementById("amount").value,
                }),
            });

            const result = await response.json();

            if (result.success) {
                document.getElementById("payment-result").textContent =
                    "支払いが完了しました!";
            } else {
                document.getElementById("payment-result").textContent =
                    "エラー: " + result.error;
            }

            document.getElementById("submit-button").disabled = false;
        }
    });
});
