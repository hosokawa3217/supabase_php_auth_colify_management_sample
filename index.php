<?php
// 環境変数からSupabase接続情報を取得
$host = getenv('DB_HOST') ?: 'aws-0-ap-northeast-1.pooler.supabase.com';
$port = getenv('DB_PORT') ?: '6543';
$dbname = getenv('DB_NAME') ?: 'postgres';
$user = getenv('DB_USER');
$password = getenv('DB_PASSWORD');
 
$dsn = "pgsql:host={$host};port={$port};dbname={$dbname};sslmode=require";
 
try {
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("データベース接続エラー: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}
 
// フォーム送信時（Create / Update / Delete）の処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
 
    if ($action === 'create' && !empty(trim($_POST['title'] ?? ''))) {
        // C: 追加 (Insert)
        $stmt = $pdo->prepare("INSERT INTO todos (title) VALUES (:title)");
        $stmt->execute([':title' => trim($_POST['title'])]);
    } elseif ($action === 'toggle' && isset($_POST['id'])) {
        // U: 完了状態切り替え (Update)
        $stmt = $pdo->prepare("UPDATE todos SET is_completed = NOT is_completed WHERE id = :id");
        $stmt->execute([':id' => (int)$_POST['id']]);
    } elseif ($action === 'delete' && isset($_POST['id'])) {
        // D: 削除 (Delete)
        $stmt = $pdo->prepare("DELETE FROM todos WHERE id = :id");
        $stmt->execute([':id' => (int)$_POST['id']]);
    }
 
    // 二重送信防止リダイレクト
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
 
// R: 一覧取得 (Select)
$todos = $pdo->query("SELECT * FROM todos ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP × Supabase TODO Web App</title>
    <style>
        body { font-family: sans-serif; max-width: 600px; margin: 40px auto; padding: 0 20px; background: #f4f6f8; }
        .card { background: #fff; padding: 24px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        form.add-form { display: flex; gap: 8px; margin-bottom: 20px; }
        input[type="text"] { flex: 1; padding: 10px; font-size: 16px; border: 1px solid #ccc; border-radius: 4px; }
        button { padding: 10px 16px; border: none; background: #2563eb; color: #fff; border-radius: 4px; cursor: pointer; }
        ul { list-style: none; padding: 0; }
        li { display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #eee; }
        .completed { text-decoration: line-through; color: #888; }
        .actions { display: flex; gap: 6px; }
        .btn-toggle { background: #10b981; }
        .btn-delete { background: #ef4444; }
    </style>
</head>
<body>
    <div class="card">
        <h1>TODO管理アプリ</h1>
 
        <!-- タスク追加フォーム (Create) -->
        <form method="POST" class="add-form">
            <input type="hidden" name="action" value="create">
            <input type="text" name="title" placeholder="新しいタスクを入力..." required>
            <button type="submit">追加</button>
        </form>
 
        <!-- タスク一覧表示 (Read) -->
        <ul>
            <?php foreach ($todos as $todo): ?>
                <li>
                    <span class="<?= $todo['is_completed'] ? 'completed' : '' ?>">
                        <?= htmlspecialchars($todo['title'], ENT_QUOTES, 'UTF-8') ?>
                    </span>
                    <div class="actions">
                        <!-- 状態更新ボタン (Update) -->
                        <form method="POST" style="margin:0;">
                            <input type="hidden" name="action" value="toggle">
                            <input type="hidden" name="id" value="<?= $todo['id'] ?>">
                            <button type="submit" class="btn-toggle"><?= $todo['is_completed'] ? '戻す' : '完了' ?></button>
                        </form>
                        <!-- 削除ボタン (Delete) -->
                        <form method="POST" style="margin:0;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $todo['id'] ?>">
                            <button type="submit" class="btn-delete">削除</button>
                        </form>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
