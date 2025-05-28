# kintai

このリポジトリは、Laravel を用いた模擬勤怠管理アプリのプロジェクトです。

## 環境構築

#### Dockerビルド

1. ```git clone git@github.com:cahoco/kintai.git```
2. ```cd coachtech-mock```
3. DockerDesktopアプリを立ち上げる
4. ```docker compose up -d```

#### Laravel 環境構築

1. ```docker compose exec php bash```

2. ```composer install```

3. ```cp .env.example .env```

4. env に以下の環境変数を追加

```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```
```
MAIL_FROM_ADDRESS=test@example.com
MAIL_FROM_NAME="Laravel App"
```

5. アプリケーションキーの作成

```
php artisan key:generate
```

6. マイグレーションの実行

```
php artisan migrate
```

7. シーディングの実行

```
php artisan db:seed
```

8. シンボリックリンク作成

```
php artisan storage:link
```

## ログイン

* 一般ユーザー
ブラウザで http://localhost/login にアクセス
- メールアドレス：test@example.com
- パスワード：00000000

* 管理者
ブラウザで http://localhost/admin/login にアクセス
- メールアドレス：admin@example.com
- パスワード：00000000


## テスト実行

```
php artisan test
```

## 使用技術

- PHP 8.2.28
- Laravel 8.83.29
- MySQL 8.0.26 
- Docker / Docker Compose
- Laravel Fortify（認証）v1.19.1
- PHPUnit（テスト）
- Mailhog（メール認証テスト用）

## テーブル設計

## ER図

## URL

* 開発環境：http://localhost/
* phpMyAdmin:：http://localhost:8080/

#### メッセージ

* 
