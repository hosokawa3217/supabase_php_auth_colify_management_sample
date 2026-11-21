# Supabase を PHP で制御するサンプル

PHP から Supabase（PostgreSQL）を操作するサンプルです。Coolify にデプロイして動かせます。

## ディレクトリ構成

```
supabase_php_colify_management_sample/
├── .env.example        # 環境変数のテンプレート（GitHubへコミット）
├── .gitignore          # コミット除外設定（.env / vendor）
├── composer.json       # 依存ライブラリ定義
├── index.php           # TODO管理Webアプリ（PDOで直接DB接続）
├── table_info.php      # テーブル/カラム情報の取得（CLI専用）
├── test_connection.php # Supabaseクライアントの初期化確認
├── config/
│   └── supabase.php    # Supabaseクライアント(PHPSupabase\Service)の初期化
└── crud/               # Supabaseクライアントによる CRUD サンプル
│   ├── select.php      # 全件取得
│   ├── insert.php      # 追加
│   ├── update.php      # 更新（ID 1 を完了に）
│   ├── delete.php      # 削除（ID 1 を削除）
│   └── reset.php       # todos を初期状態に戻す
└── auth/               # 第3回用：認証機能サンプル
├── register.php
├── login.php
├── logout.php
└── index.php
```

## セットアップ

```bash
composer install
cp .env.example .env   # SUPABASE_URL / SUPABASE_KEY / DB_* を設定
```

| 変数 | 用途 |
|---|---|
| `SUPABASE_URL`, `SUPABASE_KEY` | `config/supabase.php` と `crud/` が使用（Publishable key を設定。service_role キーは設定しない） |
| `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD` | `index.php` と `table_info.php` が PDO で使用（Transaction Pooler 推奨） |

## todos テーブル

| カラム | 型 | 備考 |
|---|---|---|
| `id` | bigint | 主キー |
| `title` | text | NOT NULL |
| `is_complete` | boolean | NOT NULL、デフォルト `false`（完了フラグはこのカラムに統一） |
| `created_at` | timestamptz | デフォルト `now()` |

## 使い方

```bash
php test_connection.php        # 接続確認
php table_info.php [スキーマ]   # テーブル情報を表示（既定: public）
php crud/select.php            # 一覧取得
php crud/insert.php            # 追加
php crud/update.php            # 更新
php crud/delete.php            # 削除
php crud/reset.php             # 初期化（要: DB関数 reset_todos_table_rpc）
php -S localhost:8000          # index.php をローカルで確認
```

- `update.php` / `delete.php` は ID 1 を固定で対象にします。
- `reset.php` は全件削除してシードデータを入れ直すため、実行前に注意してください。
- `table_info.php` はスキーマ漏えい防止のため CLI からのみ実行できます。

## デプロイ（Coolify）

GitHub の `main` へのプッシュを Webhook で受けて自動デプロイします。デプロイ時に `composer install` が実行され、`vendor/` が作られる設定にしてください（`vendor/` と `.env` は Git 管理外です）。環境変数は Coolify 側に設定します。
