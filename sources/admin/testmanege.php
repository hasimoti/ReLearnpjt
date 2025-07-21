<?php
require_once("../common/libs.php");
require_once("../common/contents_db.php");
//
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$test_obj = new ctest();
 $tests = $test_obj->get_all(false);

?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>テスト管理</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .card-title { font-weight: bold; }
    .card { min-height: 200px; }
    .test-actions a { margin-right: 10px; text-decoration: none; }
    .test-actions a.text-danger { color: red; }
  </style>
</head>
<body>
  <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>講座管理</h2>
      <a href="question_create.php" class="btn btn-primary">＋ 新規テストを追加</a>
    </div>

    <!-- 検索フォーム -->
<form method="GET" class="mb-4">
  <input type="text" name="keyword" class="form-control" placeholder="講座名やカテゴリで検索"
         value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
</form>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
      <?php foreach ($tests as $c): ?>
        <div class="col">
          <div class="card shadow-sm">
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($c['test_name']) ?></h5>
              <p class="text-muted mb-1"><?= htmlspecialchars($c['category']) ?></p>
              <p class="text-muted small mb-2">
<<<<<<< HEAD
                <?= date('Y年n月j日', strtotime($c['created_at'] ?? '')) ?>
=======
                <?= $c['is_public'] ? '公開' : '非公開' ?>
              </p>
              <div class="test-actions">
                <a href="testupload.php?pid=<?= $c['test_id'] ?>">編集</a>
                <a href="delete_test.php?id=<?= $c['test_id'] ?>" class="text-danger">削除</a>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</body>
</html>