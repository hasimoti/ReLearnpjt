<?php
session_start();

// 管理者チェック
if (empty($_SESSION['is_admin'])) {
    header('Location: admin_login.php');
    exit;
}

$pdo = new PDO('mysql:host=localhost;dbname=j2025bdb;charset=utf8', 'j2025bdb', '9yafMZ9YCfg1S16k!');
$error = '';
$success = '';

// ユーザー一覧取得
function getUsers($pdo) {
    $stmt = $pdo->query("SELECT * FROM admin_login ORDER BY ID");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 新規登録処理
if (isset($_POST['create'])) {
    $mail = $_POST['mail'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';
    $issue_password = $_POST['issue_password'] ?? '';

    if (empty($mail)) {
        $error = 'メールアドレスは必須です';
    } elseif (empty($password)) {
        $error = 'パスワードは必須です';
    } elseif ($role === 'admin' && $issue_password !== 'wiz2025') {
        $error = '発行用パスワードが正しくありません';
    } else {
        try {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO admin_login (mail, password, role, created_day, is_active) VALUES (?, ?, ?, NOW(), 1)");
$stmt->execute([$mail, $hashed, $role]);
            $success = '新規ユーザーを登録しました';
        } catch (PDOException $e) {
            $error = 'ユーザー登録に失敗しました: ' . $e->getMessage();
        }
    }
}

// 編集処理
if (isset($_POST['edit'])) {
    $id = $_POST['id'] ?? '';
    $mail = $_POST['edit_mail'] ?? '';
    $password = $_POST['edit_password'] ?? '';
    $role = $_POST['edit_role'] ?? '';

    try {
        if (!empty($password)) {
            $stmt = $pdo->prepare("UPDATE admin_login SET mail = ?, password = ?, role = ? WHERE ID = ?");
            $stmt->execute([$mail, password_hash($password, PASSWORD_DEFAULT), $role, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE admin_login SET mail = ?, role = ? WHERE ID = ?");
            $stmt->execute([$mail, $role, $id]);
        }
        $success = 'ユーザー情報を更新しました';
    } catch (PDOException $e) {
        $error = 'ユーザー情報の更新に失敗しました';
    }
}

// 削除処理
if (isset($_POST['delete'])) {
    $id = $_POST['id'] ?? '';
    try {
        $stmt = $pdo->prepare("DELETE FROM admin_login WHERE ID = ?");
        $stmt->execute([$id]);
        $success = 'ユーザーを削除しました';
    } catch (PDOException $e) {
        $error = 'ユーザーの削除に失敗しました';
    }
}

$users = getUsers($pdo);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログイン情報管理</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>ログイン情報管理</h2>
        <a href="admin_dashboard.php" class="btn btn-secondary mb-3">ダッシュボードに戻る</a>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <!-- 新規登録フォーム -->
        <div class="card mb-4">
            <div class="card-header">新規ユーザー登録</div>
            <div class="card-body">
                <form method="post" id="createForm">
                    <div class="mb-3">
                        <label for="mail" class="form-label">メールアドレス</label>
                        <input type="email" class="form-control" id="mail" name="mail" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">パスワード</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3" id="admin-issue-password-group" style="display:none;">
                        <label for="issue_password" class="form-label">発行用パスワード（管理者のみ必須）</label>
                        <input type="password" class="form-control" id="issue_password" name="issue_password">
                        
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">権限</label>
                        <select class="form-control" id="role" name="role" required>
                            <option value="admin">管理者</option>
                            <option value="user">一般ユーザー</option>
                        </select>
                    </div>
                    <button type="submit" name="create" class="btn btn-primary">登録</button>
                </form>
            </div>
        </div>

        <!-- ユーザー一覧 -->
        <h3>登録済みユーザー一覧</h3>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>メールアドレス</th>
                        <th>権限</th>
                        <th>作成日</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['ID']) ?></td>
                        <td><?= htmlspecialchars($user['mail']) ?></td>
                        <td><?= htmlspecialchars($user['role']) ?></td>
                        <td><?= htmlspecialchars($user['created_day']) ?></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $user['ID'] ?>">
                                編集
                            </button>
                            <form method="post" class="d-inline">
                                <input type="hidden" name="id" value="<?= $user['ID'] ?>">
                                <button type="submit" name="delete" class="btn btn-sm btn-danger" onclick="return confirm('本当に削除しますか？')">削除</button>
                            </form>
                        </td>
                    </tr>

                    <!-- 編集モーダル -->
                    <div class="modal fade" id="editModal<?= $user['ID'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">ユーザー情報編集</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="post">
                                    <div class="modal-body">
                                        <input type="hidden" name="id" value="<?= $user['ID'] ?>">
                                        <div class="mb-3">
                                            <label class="form-label">メールアドレス</label>
                                            <input type="email" class="form-control" name="edit_mail" value="<?= htmlspecialchars($user['mail']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">新しいパスワード（変更する場合のみ）</label>
                                            <input type="password" class="form-control" name="edit_password">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">権限</label>
                                            <select class="form-control" name="edit_role">
                                                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>管理者</option>
                                                <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>一般ユーザー</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                                        <button type="submit" name="edit" class="btn btn-primary">更新</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // 管理者選択時のみ発行用パスワード欄を表示
    document.getElementById('role').addEventListener('change', function() {
        var issueGroup = document.getElementById('admin-issue-password-group');
        if(this.value === 'admin') {
            issueGroup.style.display = '';
            document.getElementById('issue_password').setAttribute('required', 'required');
        } else {
            issueGroup.style.display = 'none';
            document.getElementById('issue_password').removeAttribute('required');
        }
    });
    window.addEventListener('DOMContentLoaded', function() {
        document.getElementById('role').dispatchEvent(new Event('change'));
    });
    </script>
</body>
</html>