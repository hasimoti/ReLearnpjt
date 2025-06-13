<?php
session_start();
if (empty($_SESSION['is_admin'])) {
    header('Location: admin_login.php');
    exit;
}
?>
<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>管理者ダッシュボード</title>
    <style>
        .header {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            background: #fff;
            padding: 10px 20px;
        }
        .header a {
            color: #000;
            text-decoration: none;
            margin-left: 30px;
            font-weight: bold;
        }
        .header a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="admin_dashboardlog.php">ホーム</a>
        <a href="admin_login_manage.php">ログイン情報</a>
        <a href="course_manage.php">講座管理</a>
        <a href="test_manage.php">テスト管理</a>
        <a href="log.php">ログ</a>
    </div>
    <h1>管理者ダッシュボード</h1>
    <p>ようこそ、管理者さん！</p>
</body>
</html>