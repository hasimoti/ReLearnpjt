<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=j2025bdb;charset=utf8', 'j2025bdb', '9yafMZ9YCfg1S16k!');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mail = $_POST['mail'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM login WHERE mail = ?");
    $stmt->execute([$mail]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['is_admin'] = true;
        header('Location: admin_dashboard.php');
        exit;
    } else {
        $error = 'メールアドレスまたはパスワードが違います';
    }
}
?>
<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>管理者ログイン</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <h2>管理者ログイン</h2>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="post" class="mt-4">
                <div class="mb-3">
                    <label for="mail" class="form-label">メールアドレス</label>
                    <input type="email" class="form-control mx-auto" id="mail" name="mail" required autofocus maxlength="255" style="max-width:300px;">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">パスワード</label>
                    <input type="password" class="form-control mx-auto" id="password" name="password" required maxlength="50" style="max-width:300px;">
                </div>
                <button type="submit" class="btn btn-primary">ログイン</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>