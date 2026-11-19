Supabase を　PHP で制御するサンプルコード


1. プロジェクトのディレクトリ構造作成
ルートディレクトリを作成し、役割ごとのフォルダを準備します。
supabase-php-sample/
├── .env.example        # 環境変数のテンプレート（GitHubへコミット）
├── .gitignore          # コミット除外設定
├── composer.json       # 依存ライブラリ定義
├── config/             # 共通接続設定
│   └── supabase.php    # Supabaseインスタンス初期化処理
├── crud/               # 第2回用：CRUD基本操作サンプル
│   ├── select.php
│   ├── insert.php
│   ├── update.php
│   └── delete.php
└── auth/               # 第3回用：認証機能サンプル
    ├── register.php
    ├── login.php
    ├── logout.php
    └── index.php

