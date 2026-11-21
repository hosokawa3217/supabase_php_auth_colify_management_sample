<?php
// 共通接続モジュール
// .env ファイルから接続情報を自動取得し、Supabaseクライアント（PHPSupabase\Service）のインスタンスを生成する共通処理

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use PHPSupabase\Service;

// .env ファイルの読み込み
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

$supabaseUrl = $_ENV['SUPABASE_URL'] ?? null;
$supabaseKey = $_ENV['SUPABASE_KEY'] ?? null;

// 環境変数の未設定チェック
if (!$supabaseUrl || !$supabaseKey || $supabaseUrl === 'https://your-project-id.supabase.co') {
    die('エラー: .env ファイルに正しい SUPABASE_URL および SUPABASE_KEY を設定してください。' . PHP_EOL);
}

try {
    // Supabase サービスの初期化
    // ※第1引数に API Key (Publishable key)、第2引数に URL を渡します
    $service = new Service($supabaseKey, $supabaseUrl);
} catch (Exception $e) {
    die('Supabase接続初期化エラー: ' . $e->getMessage() . PHP_EOL);
}