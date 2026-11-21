<?php
session_start();
require_once __DIR__ . '/../config/supabase.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($email) && !empty($password)) {
        try {
            $auth = $service->createAuth();
            // Supabase Auth へユーザー作成リクエスト
            $auth->createUserWithEmailAndPassword($email, $password);
            $data = $auth->data();

            $message = "✅ ユーザー登録が完了しました！ログインを行ってください。";
        } catch (Exception $e) {
            $error = "登録エラー: " . $auth->getError();
        }
    } else {
        $error = "メールアドレスとパスワードを入力してください。";
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>新規ユーザー登録 - auth/register.php</title>
</head>
<body>
    <h2>新規ユーザー登録</h2>
    <?php if ($message): ?><p style="color: green;"><?= htmlspecialchars($message) ?></p><?php endif; ?>
    <?php if ($error): ?><p style="color: red;"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    
    <form method="POST" action="register.php">
        <div>
            <label>メールアドレス:</label><br>
            <input type="email" name="email" required>
        </div>
        <div>
            <label>パスワード:</label><br>
            <input type="password" name="password" required minlength="6">
        </div>
        <br>
        <button type="submit">登録する</button>
    </form>
    <p><a href="login.php">すでにアカウントをお持ちの方（ログイン）</a></p>
</body>
</html>
