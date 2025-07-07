<?php
require_once("common/libs.php");
$test_id = intval($_GET['test_id']);

// DB接続
$pdo = new PDO("mysql:host=localhost;dbname=your_db;charset=utf8", "user", "pass");

// テスト本体
$stmt = $pdo->prepare("SELECT * FROM tests WHERE test_id = ?");
$stmt->execute([$test_id]);
$test = $stmt->fetch(PDO::FETCH_ASSOC);

// 質問一覧
$stmt = $pdo->prepare("SELECT * FROM questions WHERE test_id = ?");
$stmt->execute([$test_id]);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 質問ごとに選択肢 or 記述を追加
foreach ($questions as &$q) {
    if ($q['type'] === 'choice') {
        $stmt = $pdo->prepare("SELECT * FROM choices WHERE question_id = ?");
        $stmt->execute([$q['question_id']]);
        $q['choices'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } elseif ($q['type'] === 'text') {
        $stmt = $pdo->prepare("SELECT * FROM text_answers WHERE question_id = ?");
        $stmt->execute([$q['question_id']]);
        $q['text_answer'] = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

echo json_encode([
  'test' => $test,
  'questions' => $questions
]);