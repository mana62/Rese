# Rese
飲食店予約アプリ<br>

# 作成した目的
初年度でのユーザー数10,000人達成の為<br>

# アプリケーションURL
<開発環境><br>
・phpmyadmin：<br>
・アプリurl：http://localhost/<br>
<br>
・phpmyadmin：<br>
・アプリurl：<br>

# 他のリポジトリ
<開発環境><br>
https://github.com/mana62/Rese<br>
<br>
<本番環境><br>


# 機能一覧
・会員登録<br>
・ログイン<br>
・ログアウト<br>
・ユーザー情報取得<br>
・ユーザー飲食店お気に入り一覧取得<br>
・ユーザー飲食店予約情報取得<br>
・飲食店一覧取得<br>
・飲食店詳細取得<br>
・飲食店お気に入り追加<br>
・飲食店お気に入り削除<br>
・飲食店予約情報追加<br>
・飲食店予約情報削除<br>
・エリアで検索する<br>
・ジャンルで検索する<br>
・店名で検索する<br>
・予約変更機能<br>
・評価機能<br>
・バリデーション<br>
・レスポンシブデザイン<br>
・管理画面<br>
・ストレージ<br>
・メール認証<br>
・メール送信<br>
・リマインダー<br>
・QRコード<br>
・決済機能(stripe)<br>
・環境の切り分け<br>

# 使用技術
・nginx: latest<br>
・php: 8.1-fpm<br>
・mysql: 8.0.26<br>
・Laravel: 8<br>

# テーブル設計
[rese-table.pdf](https://github.com/user-attachments/files/17808046/rese-table.pdf)

# ER図
[rese-ER.pdf](https://github.com/user-attachments/files/17808041/rese-ER.pdf)


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

# その他
・メール認証をしていないとログインできない<br>
・会員ユーザーでないとお店のお気に入り機能は使えない<br>
・会員ユーザーでないとレビューの投稿はできない<br>
・レストランの画像を保存するには、detailページの画像を保存を押すと、storage/app/public/restaurantsに保存される<br>
・予約をキャンセルするとrestaurantテーブルのstatusカラムがcancelに変わる<br>
・adminまたはrestaurantのownerの権限があれば、それぞれのページが閲覧できる<br>
<br>
「役割の変え方」<br>

1. docker exec -it rese_php bash<br>
2. php artisan tinker<br>
3. $user = \App\Models\User::find(1);<br>
(変更したいユーザーIDを選ぶ)<br>
4. $user->role = 'admin' または 'store-owner';<br>
5. $user->save();<br>