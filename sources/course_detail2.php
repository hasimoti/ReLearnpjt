<?php
/*!
@file member_detail.php
@brief メンバー詳細（講座IDをURLパラメータで指定可能）
@copyright Copyright (c) 2024 Yamanoi Yasushi.
*/

// ライブラリをインクルード
require_once("common/libs.php");
require_once("common/contents_db.php");

$err_array = array();
$err_flag = 0;
$page_obj = null;
$member_id = 0;

//--------------------------------------------------------------------------------------
/// 本体ノード
//--------------------------------------------------------------------------------------
class cmain_node extends cnode {
    //--------------------------------------------------------------------------------------
    /*!
    @brief コンストラクタ
    */
    //--------------------------------------------------------------------------------------
    public function __construct() {
        parent::__construct();
        global $member_id, $course_id;

        // member_idはGETかPOSTから取得（必要なら）
        if (isset($_GET['mid']) && cutil::is_number($_GET['mid']) && $_GET['mid'] > 0) {
            $member_id = $_GET['mid'];
        }
        if (isset($_POST['member_id']) && cutil::is_number($_POST['member_id']) && $_POST['member_id'] > 0) {
            $member_id = $_POST['member_id'];
        }

        // 講座IDはURLパラメータ 'cid' から取得。無ければ1をデフォルトに
        $course_id = 1;
        if (isset($_GET['cid']) && cutil::is_number($_GET['cid']) && $_GET['cid'] > 0) {
            $course_id = $_GET['cid'];
        }
        // グローバルに保存
        $GLOBALS['course_id'] = $course_id;
    }

    //--------------------------------------------------------------------------------------
    /*!
    @brief POST変数のデフォルト値をセット
    @return なし
    */
    //--------------------------------------------------------------------------------------
    public function post_default() {
        cutil::post_default("member_name", '');
    }

    //--------------------------------------------------------------------------------------
    /*!
    @brief 本体実行（表示前処理）
    @return なし
    */
    //--------------------------------------------------------------------------------------
    public function execute() {
        global $err_array, $err_flag, $page_obj, $course_id;

        // 講座情報取得（IDは動的）
        $course_obj = new ccourse();
        $course_data = $course_obj->get_tgt(false, $course_id);
        if (!cutil::array_chk($course_data)) {
            echo '講座が見つかりません。';
            exit;
        }
        $_POST['course_data'] = $course_data;
    }

    //--------------------------------------------------------------------------------------
    /*!
    @brief 表示(継承して使用)
    @return なし
    */
    //--------------------------------------------------------------------------------------
    public function display() {
        global $member_id;
?>
<!-- コンテンツ -->
<link rel="stylesheet" type="text/css" href="css/course_detail.css">
<div class="contents">
<h5><strong>講座詳細</strong></h5>
<form name="form1" action="<?= $_SERVER['PHP_SELF']; ?>" method="post">
<a href="member_list.php">一覧に戻る</a>

<!-- 講座タイトルと説明を表示 -->
<h3><?= htmlspecialchars($_POST['course_data']['course_name'] ?? 'タイトルなし') ?></h3>
<p><?= nl2br(htmlspecialchars($_POST['course_data']['description'] ?? '説明なし')) ?></p>

<!-- Google Drive 埋め込み対応 -->
<?php
$drive_id = $_POST['course_data']['drive_file_id'] ?? '1Hzq2woyQVI1kzC2YqCQPRbfnUqRiLCXP';
$iframe_url = "https://drive.google.com/file/d/{$drive_id}/preview";
?>
<iframe width="660" height="415" id="videoFrame" class="mov"
        src="<?= $iframe_url ?>"
        title="Google Drive video player" frameborder="0"
        allow="autoplay; encrypted-media"
        allowfullscreen></iframe>

<!-- タイムスタンプ例 -->
<div class="summary">
    <div class="timestamp">
        <a>0:00 - 開始</a>
        <a>0:04 - 曲が流れる</a>
        <a>3:00 - 社内ルール</a>
        <a>5:00 - 質疑応答</a>
    </div>
</div>

<input type="hidden" name="func" value="" />
<input type="hidden" name="param" value="" />
<input type="hidden" name="member_id" value="<?= $member_id; ?>" />
</form>
</div>
<?php
    }

    public function __destruct() {
        parent::__destruct();
    }
}

// ページを作成
$page_obj = new cnode();
$page_obj->add_child(cutil::create('cheader'));
$page_obj->add_child($cmain_obj = cutil::create('cmain_node'));
$page_obj->add_child(cutil::create('cfooter'));
$page_obj->create();
$cmain_obj->execute();
$page_obj->display();
?>
