<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // ログインしていない、または管理者じゃない場合
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>ユーザー登録（管理者用）</title>
</head>
<body>
  <h2>新しいユーザーを登録する</h2>
  <form action="register_user_process.php" method="POST">
    <label>メールアドレス: <input type="email" name="email" required></label><br>
    <label>パスワード: <input type="password" name="password" required></label><br>
    <button type="submit">登録</button>
  </form>
</body>
</html>
