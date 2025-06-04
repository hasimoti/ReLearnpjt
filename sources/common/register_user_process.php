<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit;
}

$email = $_POST['email'];
$password = $_POST['password'];
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// データベース接続
$pdo = new PDO('mysql:host=localhost;dbname=login_db;charset=utf8', 'root', '');

// メールが重複していないか確認
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    echo "このメールアドレスは既に登録されています。";
    exit;
}

// ユーザーを追加
$stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
$result = $stmt->execute([$email, $hashed_password]);

if ($result) {
    echo "ユーザーを登録しました。";
} else {
    echo "登録に失敗しました。";
}
?>
