"use strict";

document.addEventListener("DOMContentLoaded", function () {
    const stripePublicKey = window.stripePublicKey;
    if (!stripePublicKey) {
        document.getElementById("payment-result").textContent =
            "Stripe公開キーが見つかりません";
        return;
    }
    const stripe = Stripe(stripePublicKey);
    const elements = stripe.elements();
    const cardElement = elements.create("card");
    cardElement.mount("#card-element");
    const form = document.getElementById("payment-form");
    const resultElement = document.getElementById("payment-result");

    form.addEventListener("submit", async (event) => {
        event.preventDefault();
        resultElement.textContent = "処理中です...";

        try {
            const response = await fetch("/process-payment", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                },
                body: JSON.stringify({
                    reservation_id:
                        document.getElementById("reservation-id").value,
                    payment_method: "pm_card_visa",
                    amount: parseInt(
                        document.getElementById("amount").value,
                        10
                    ),
                    currency: "jpy",
                }),
            });
            const data = await response.json();
            if (data.success) {
                if (data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else {
                    resultElement.textContent =
                        "お支払いが完了しましたが、リダイレクト先が見つかりません";
                }
            } else {
                resultElement.textContent =
                    data.message || "エラーが発生しました";
            }
        } catch (error) {
            resultElement.textContent = `エラー: ${error.message}`;
        }
    });
});
