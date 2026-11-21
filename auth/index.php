<?php
session_start();
require_once __DIR__ . '/../config/supabase.php';

// 1. 未ログインチェック
if (!isset($_SESSION['access_token']) || empty($_SESSION['access_token'])) {
    header('Location: login.php');
    exit;
}

$bearerToken = $_SESSION['access_token'];
$user = $_SESSION['user'] ?? null;

try {
    // 2. アクセストークンの有効性を Supabase Auth で検証
    $auth = $service->createAuth();
    $userData = $auth->getUser($bearerToken);
} catch (Exception $e) {
    // トークン期限切れや無効な場合はログアウト処理へ
    header('Location: logout.php');
    exit;
}

// 3. RLS連携：アクセストークンをセットして DB を初期化
$db = $service->setBearerToken($bearerToken)->initializeDatabase('todos', 'id');
$todos = $db->fetchAll()->getResult();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>マイページ・タスク管理 - auth/index.php</title>
</head>
<body>
    <h2>👤 マイページ（ログイン済み保護ページ）</h2>
    <p>ようこそ、<strong><?= htmlspecialchars($userData->email ?? $user->email) ?></strong> さん！</p>
    <p>ユーザーID (UUID): <code><?= htmlspecialchars($userData->id ?? $user->id) ?></code></p>
    <hr>

    <h3>📋 あなたの個人タスク一覧（RLS検証）</h3>
    <?php if (empty($todos)): ?>
        <p>タスクはありません（または所有権のあるタスクが存在しません）。</p>
    <?php else: ?>
        <ul>
            <?php foreach ($todos as $todo): ?>
                <li>
                    ID: <?= $todo->id ?> | 
                    <?= $todo->is_complete ? '【完了】' : '【未完了】' ?> 
                    <?= htmlspecialchars($todo->title) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <p><a href="logout.php">🚪 ログアウト</a></p>
</body>
</html>
