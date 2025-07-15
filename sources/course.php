<?php
class ccourse {
    public function get_all($dummy = false) {
        // DBから講座一覧を取得
        $pdo = cdbutil::get_pdo(); // ※cdbutil::get_pdo()はプロジェクトのDBユーティリティに合わせてください
        $sql = "SELECT * FROM course ORDER BY course_id DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>