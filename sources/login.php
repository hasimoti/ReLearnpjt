<?php
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mail = $_POST['mail'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($mail === '' || $password === '') {
        $error = "メールアドレスとパスワードを入力してください。";
    } else {
        try {
            // ✅ データベース接続情報（環境にあわせて変更）
            $dsn = 'mysql:host=localhost;dbname=your_db_name;charset=utf8';
            $db_user = 'j2025bdb';
            $db_pass = '9yafMZ9YCfg1S16k!';

            $pdo = new PDO('mysql:host=localhost;dbname=j2025bdb;charset=utf8', 'j2025bdb', '9yafMZ9YCfg1S16k!');

            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // ✅ 有効なユーザーのみを検索
            $stmt = $pdo->prepare("SELECT * FROM admin_login WHERE mail = :mail AND is_active = 1");
            $stmt->bindValue(':mail', $mail, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // ✅ ログイン成功時
                $_SESSION['user_logged_in'] = true;
                $_SESSION['user_id'] = $user['ID'];
                $_SESSION['mail'] = $user['mail'];
                $_SESSION['role'] = $user['role'];

                header("Location: index.php"); // ログイン後に遷移させたいページ
                exit();
            } else {
                $error = "ログイン情報が正しくありません。";
            }

        } catch (PDOException $e) {
            $error = "データベース接続エラー: " . $e->getMessage();
        }
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
            <h2>ログイン</h2>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="post" class="mt-4">
    <div class="mb-3">
        <label for="mail" class="form-label">メールアドレス</label>
        <input type="text" class="form-control mx-auto" id="mail" name="mail" required autofocus maxlength="255" style="max-width:300px;">
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