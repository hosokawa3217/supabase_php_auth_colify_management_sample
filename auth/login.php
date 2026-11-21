<?php
session_start();
require_once __DIR__ . '/../config/supabase.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($email) && !empty($password)) {
        try {
            $auth = $service->createAuth();
            // メールアドレスとパスワードでサインイン
            $auth->signInWithEmailAndPassword($email, $password);
            $data = $auth->data();

            if (isset($data->access_token)) {
                // セッションにアクセストークンとユーザー情報を保持
                $_SESSION['access_token'] = $data->access_token;
                $_SESSION['user'] = $data->user;

                // マイページ（保護ページ）へリダイレクト
                header('Location: index.php');
                exit;
            } else {
                $error = "ログインに失敗しました。アクセストークンが取得できません。";
            }
        } catch (Exception $e) {
            $error = "認証エラー: " . $auth->getError();
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
    <title>ログイン - auth/login.php</title>
</head>
<body>
    <h2>ユーザーログイン</h2>
    <?php if ($error): ?><p style="color: red;"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    
    <form method="POST" action="login.php">
        <div>
            <label>メールアドレス:</label><br>
            <input type="email" name="email" required>
        </div>
        <div>
            <label>パスワード:</label><br>
            <input type="password" name="password" required>
        </div>
        <br>
        <button type="submit">ログイン</button>
    </form>
    <p><a href="register.php">新規アカウント登録はこちら</a></p>
</body>
</html>
