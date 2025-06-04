<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=login_db;charset=utf8', 'root', '');

$email = $_POST['email'];
$password = $_POST['password'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    echo "ログイン成功";
    // 例：header("Location: /index.php");
} else {
    echo "ログイン失敗：メールかパスワードが違います";
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