# Rese
- 飲食店予約アプリ<br>
<br>

# 作成した目的
- 外部の飲食店予約サービスは手数料を取られるので自社で予約サービスを持ちたいため<br>
<br>

# アプリケーションURL
<開発環境><br>
- phpmyadmin: [http://localhost:8080](http://localhost:8080)<br>
- アプリURL: [http://localhost/register](http://localhost/)
<br>

# 他のリポジトリ
<開発環境><br>
- https://github.com/mana62/Rese<br>
<br>

# 機能一覧
- 会員登録<br>
- ログイン<br>
- ログアウト<br>
- ユーザー情報取得<br>
- ユーザー飲食店お気に入り一覧取得<br>
- ユーザー飲食店予約情報取得<br>
- 飲食店一覧取得<br>
- 飲食店詳細取得<br>
- 飲食店お気に入り追加<br>
- 飲食店お気に入り削除<br>
- 飲食店予約情報追加<br>
- 飲食店予約情報削除<br>
- エリアで検索する<br>
- ジャンルで検索する<br>
- 店名で検索する<br>
- 予約変更機能<br>
- 評価機能<br>
- バリデーション<br>
- レスポンシブデザイン<br>
- 管理画面<br>
- ストレージ<br>
- メール認証<br>
- メール送信<br>
- リマインダー<br>
- QRコード<br>
- 決済機能(stripe)<br>
- 環境の切り分け<br>
<br>

# 使用技術
- nginx: latest<br>
- php: 8.1-fpm<br>
- mysql: 8.0.26<br>
- Laravel: 8<br>
<br>

# テーブル設計
<img width="820" alt="rese-table" src="https://github.com/user-attachments/assets/0f6eaf22-c8bb-49e1-ab09-c0c63880f7fd" />
<br>

# ER図
<img width="899" alt="rese-er" src="https://github.com/user-attachments/assets/4dd206af-3b61-41e0-81d6-6eda679cf852" />
<br>

# 環境構築
1. リモートリポジトリを作成<br>
2. ローカルリポジトリの作成<br>
3. リモートリポジトリをローカルリポジトリに追加<br>
4. docker-compose.yml の作成<br>
5. Nginx の設定<br>
6. PHP の設定<br>
7. MySQL の設定<br>
8. phpMyAdmin の設定<br>
9. docker-compose up -d --build<br>
10. docker-compose exec php bash<br>
11. composer create-project "laravel/laravel=8.*" . --prefer-dist<br>
12. app.php の timezone を修正<br>
13. .env ファイルの環境変数を変更<br>
14. php artisan migrate<br>
15. php artisan db:seed<br>
<br>

# クローンの流れ
1. Git リポジトリのクローン<br>
(git clone git@github.com:mana62/Rese.git)<br>
2. .env ファイルの作成<br>
(cp .env.example .env)<br>
3. .env ファイルの編集<br>
<br>
DB_CONNECTION=mysql<br>
DB_HOST=mysql<br>
DB_PORT=3306<br>
DB_DATABASE=rese_local<br>
DB_USERNAME=user<br>
DB_PASSWORD=pass<br>
<br>
MAIL_MAILER=smtp<br>
MAIL_HOST=mailhog<br>
MAIL_PORT=1025<br>
MAIL_USERNAME=null<br>
MAIL_PASSWORD=null<br>
MAIL_ENCRYPTION=null<br>
MAIL_FROM_ADDRESS=test@example.com<br>
MAIL_FROM_NAME="RESE"<br>
<br>
STRIPE_KEY=pk_test_51QL1HQP6vhR18R0Qov3GuXbuoeGRm0Zd0IYuwgCjjWg44xtgaw797DG6oOubHaDEHvmMMmFa6qRQcMeSHqvgOBL900AcnURSH7<br>
<br>
STRIPE_SECRET=sk_test_51QL1HQP6vhR18R0Q48Wf9g24z9MwM107D1wPfFXi0J8uWlyF2xY4vZxMBLyq6lgE7VPQzMdj46oiV8vmRRvUkS3X00OVvjw1zF<br>
<br>

4. Dockerの設定<br>
(docker compose up -d --build)<br>
5. PHPコンテナにアクセス<br>
(docker exec -it rese_php bash)<br>
6. Laravelパッケージのインストール<br>
(composer install)<br>
7. アプリケーションキーの生成<br>
(php artisan key:generate)<br>
8. マイグレーション<br>
(php artisan migrate)<br>
9. シーディング<br>
(php artisan db:seed)<br>
10. シンボリックリンクの作成<br>
(php artisan storage:link)<br>
11. パーミッションの確認<br>
(chmod -R 775 storage)<br>
(chmod -R 775 public/storage)<br>


# 補足
[メール認証]<br>
- メール認証をしていないとログインできない<br>

[非会員ユーザー]<br>
- 会員ユーザーでないとお店のお気に入り機能は使えない<br>
- 会員ユーザーでないとレビューの投稿はできない<br>
（レビューを一覧することは可能）<br>

[レストランの画像をストレージに保存]<br>
- レストランの画像を保存するには、detailページの画像を保存を押すと、storage/app/public/restaurantsに保存される<br>

[予約キャンセル]<br>
- 予約をキャンセルするとrestaurantテーブルのstatusカラムがcancelに変わり、画面からは無くなる<br>

[お支払い]<br>
- マイページのお支払い部分はお支払い（任意）するとマイページの支払いへ進むの部分が支払い済みに変わる<br>

[管理者]<br>
- admin専用のログイン画面から、特定のパスワードを入力すると、管理者画面へとべる<br>
（adminのパスワード：admin_pass）<br>
- 管理者はお知らせメールを管理者画面から送ることができる<br>
<br>

[店舗オーナー]<br>
- 店舗オーナー専用のログイン画面から、管理者が作った際の、メールアドレス、パスワードを入れればログイン画面に入れれば閲覧が可能<br>
- 一人のオーナーが複数のレストラを作成した場合は、店舗検索部分から特定のレストランを検索し、更新と予約一覧を見ることができる<br>
（デフォルトの更新部分には一番はじめに作成したレストランが表示されている）<br>

[リマインダー]<br>
- 予約当日の朝7:00に設定<br>
(店舗オーナーとユーザーに送られますが、店舗オーナーはadminで作成したオーナーにのみ送られます)<br>


