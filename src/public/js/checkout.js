document.addEventListener("DOMContentLoaded", function () {
    //DOMContentLoaded = HTMLが読み込まれたときに実行
    const stripePublicKey = window.stripePublicKey;

    //stripeの公開キーがないときのエラー
    if (!stripePublicKey) {
        document.getElementById("payment-result").textContent =
            "Stripe公開キーが見つかりません";
        return;
    }

    //stripeを初期化
    const stripe = Stripe(stripePublicKey);

    //stripe用の要素
    const elements = stripe.elements();

    //カード情報を入力する欄
    const cardElement = elements.create("card");

    //HTMLのcard-elementにカード入力欄を表示
    cardElement.mount("#card-element"); //.mount = HTMLの中に設置するメソッド

    //bladeからidで取得
    const form = document.getElementById("payment-form");
    const resultElement = document.getElementById("payment-result");

    //ボタンのイベント
    form.addEventListener("submit", async (event) => {
        //async = 非同期処理、時間がかかる作業（サーバーへのリクエストなど）が終わるのを待たず、他の処理を続けられる仕組み
        event.preventDefault(); //ページリロードを防ぐ
        resultElement.textContent = "処理中です..."; //bladeのテキストをクリックしたら書き換え

        try {
            //サーバーに支払いリクエストを送信
            const response = await fetch("/process-payment", {
                //await = サーバーからの返事を待ってから次の処理を進める
                method: "POST",
                headers: {
                    "Content-Type": "application/json", //サーバーに送るデータはJSON形式と伝える
                    "X-CSRF-TOKEN": document.querySelector(
                        //laravelが用意しているセキュリティ
                        'meta[name="csrf-token"]'
                    ).content,
                },
                //JSON形式に変換
                body: JSON.stringify({
                    reservation_id:
                        document.getElementById("reservation-id").value,
                    payment_method: "pm_card_visa", //テスト用のid
                    amount: parseInt(
                        document.getElementById("amount").value,
                        10
                    ),
                    currency: "jpy",
                }),
            });

            // HTTPエラーハンドリング
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.error || "エラーが発生しました");
            }

            const data = await response.json();
            resultElement.textContent = data.success
                ? data.message
                : data.error || data.message;
        } catch (error) {
            resultElement.textContent = `エラー: ${error.message}`;
        }
    });
});
