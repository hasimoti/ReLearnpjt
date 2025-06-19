<?php
//ライブラリをインクルード
require_once("../common/libs.php");

session_start();

$pdo = new PDO('mysql:host=localhost;dbname=j2025bdb;charset=utf8', 'j2025bdb', '9yafMZ9YCfg1S16k!');

$err_array = array();
$err_flag = 0;
$page_obj = null;

$ERR_STR = "";
$admin_master_id = "";
$admin_name = "";

class cmain_node extends cnode {
    public function execute(){
        global $ERR_STR;
        global $admin_master_id;
        global $admin_name;

        


$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mail = $_POST['mail'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM admin_login WHERE mail = ?");
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
}

    public function display(){
        global $ERR_STR;
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>管理者ログイン</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="text-center mb-4">管理者ログイン</h2>
                <?php if($ERR_STR): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($ERR_STR); ?></div>
                <?php endif; ?>
                <form method="post">
                    <div class="mb-3">
                        <label for="mail" class="form-label">メールアドレス</label>
                        <input type="email" class="form-control" id="mail" name="mail" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">パスワード</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">ログイン</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
<?php
	}
}

//ページを作成
$page_obj = new cnode();
//本体追加
$page_obj->add_child($main_obj = cutil::create('cmain_node'));
//構築時処理
$page_obj->create();
//本体実行（表示前処理）
$main_obj->execute();
//ページ全体を表示
$page_obj->display();
?>