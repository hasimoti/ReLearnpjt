<?php
// セッション開始
session_start();

// ログイン処理（例：POST送信時）
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    // ここで認証処理（例：DB照合など）を行う
    // 仮の認証例
    if ($username === 'admin' && $password === 'password') {
        $_SESSION['user'] = $username;
        header('Location: contents_nodes.php');
        exit;
    } else {
        $error = 'ユーザー名またはパスワードが違います';
    }
}
?>
<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>ログイン</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <h2>ログイン<img src="../img/logo.png" alt="サンプル画像" width="38" height="38"></h2>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="post" class="mt-4">
                <div class="mb-3 ">
                    <label for="username" class="form-label">ユーザー名</label>
                    <input type="text" class="form-control mx-auto" id="username" name="username" required autofocus maxlength="20" style="max-width:300px;">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">パスワード</label>
                    <input type="password" class="form-control mx-auto" id="password" name="password" required maxlength="20" style="max-width:300px;">
                </div>
        <button type="submit" class="btn btn-primary">ログイン</button>
    </form>
</div>

</body>
</html>