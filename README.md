# アプリケーション名

## FleaMarket

Laravel × Stripe × Dockerで構築したフリマアプリ

---

## 概要

以下の機能を実装したフリマアプリです<br>

- ユーザー登録
- メール認証
- マイページ（出品/購入一覧表示）
- 商品一覧・検索・詳細表示
- 出品/購入機能
- stripe決済（コンビニ/クレカ払い）
- お気に入り（いいね）機能
- コメント機能
- ユーザー評価
- 取引チャット

---

# 環境構築手順

## 1. MakeFile実行

本プロジェクトでは開発環境とテスト環境の完全自動構築をMakefileにまとめています。<br>
make init の1コマンドで開発環境 + テスト環境がすぐに利用可能です。

```bash
git clone https://github.com/e-kasai/laravel-flea-market.git

cd laravel-flea-market
make init
```

## 2.Stripe設定(必須)

- [Stripe Dashboard](https://dashboard.stripe.com/test/apikeys) からキーを取得し`.env`に追加してください。<br>

```php
# .env
STRIPE_KEY=       # pkより始まる公開可能キー
STRIPE_SECRET=    # skより始まるシークレットキー
```

**補足**

- DB設定とMailHog設定はローカルのみの為、既に入力済みです。
- `.env.testing.example`に、Stripeダミーキーを事前にいれてあるため、<br>
  `.env.testing`に Stripeキーの実際の値を設定する必要はありません。<br>

# 環境構築は以上です

# テスト実行

```bash
docker compose exec php bash
php artisan test
```

# 環境をクリーンに戻す必要が出たとき

```bash
docker compose exec php bash
php artisan migrate:fresh --seed
```

---

# 使用技術

- Laravel 8.83.8
- PHP 8.1.33
- MySQL 8.0.26
- Docker/docker-compose
- MailHog v1.0.1
- Stripe
- JavaScript(ブラウザ実行)
- Node.js(Prettier 用)

---

# ER 図

![ER図](./docs/latest_er.png)

# URL

- 開発環境：http://localhost/
- phpMyAdmin：http://localhost:8080/

## 開発用ログイン情報

Seeder により以下の4ユーザー（メール認証済み）が自動作成されます。<br>
以下は開発用のダミーアカウントであり、本番環境とは無関係です。<br>

#### 管理者ユーザー

- メール: admin_user@example.com

#### ユーザーA（出品数5）

- メール: userA@example.com

#### ユーザーB（出品数5）

- メール: userB@example.com

#### ユーザーC（出品数0）

- メール: userC@example.com

パスワードは全て"password"です。

---

# 補足：仕様について

## 1. Stripe決済

Stripe Checkout を利用し、クレジットカード決済・コンビニ決済に対応しています。<br>
開発環境では Webhook を使用せず、購入確定を簡易フローで処理する構成としています。<br>
テストカード番号（4242 4242 4242 4242）で動作確認できます。<br>

## 2. レスポンシブ対応の基準設定

最新スマートフォンの画面幅を基準にし、スマホ・タブレット・PC・大画面の4段階でレイアウトを最適化しています。

## 3. アップロードサイズ制限

本アプリでは商品画像などのアップロードを想定しているため、<br>
Nginx/PHP のアップロードサイズ制限を6MBに統一しています(validationの５MB＋１MBバッファ)

| チェック項目                   | コマンド / ファイル  | 設定値 |
| ------------------------------ | -------------------- | ------ |
| Nginx の client_max_body_size  | nginx.conf           | 6 MB   |
| PHP の upload_max_filesize     | php.ini              | 6 MB   |
| PHP の post_max_size           | php.ini              | 6 MB   |

## 4. 購入処理の責務分離 (Serviceクラスの使用)

Stripe決済処理や購入確定処理はServiceクラスに分離し、<br>
コントローラーはフロー制御に専念する構成としています。<br>
役割を分離することで保守性と可読性を向上しています。<br>

## 5. マイリスト機能の挙動

ログイン状態とメール認証状態に応じて、<br>
「おすすめ／マイリスト」の表示内容を切り替えています。<br>
未認証ユーザーにはマイリストを非表示としています。<br>

## 6. ルート設計

Laravelのルートモデルバインディングを使用し<br>
ルートパラメータを{item}に統一しています。<br>
Item $item を自動解決できるため、より安全で簡潔な実装になります。
