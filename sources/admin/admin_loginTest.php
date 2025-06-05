<?php
<?php
// テスト用のメールアドレスとパスワード
$mail = 'yourmail@example.com';
$password = 'adminpass';

// DB接続
$pdo = new PDO('mysql:host=localhost;dbname=j2025bdb;charset=utf8', 'j2025bdb', '9yafMZ9YCfg1S16k!');

// ユーザー取得
$stmt = $pdo->prepare("SELECT * FROM login WHERE mail = ?");
$stmt->execute([$mail]);
$admin = $stmt->fetch();

if ($admin && password_verify($password, $admin['password'])) {
    echo "ログイン成功\n";
} else {
    echo "ログイン失敗\n";
}