// 接続確認テストスクリプトの作成 (test_connection.php)
// 共通モジュールがエラーなく読み込めるかテスト

<?php

require_once __DIR__ . '/config/supabase.php';

echo "✅ Supabase サービスインスタンスの初期化に成功しました！" . PHP_EOL;
echo "接続先URL: " . $_ENV['SUPABASE_URL'] . PHP_EOL;