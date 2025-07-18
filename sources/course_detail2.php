<?php
/*!
@file member_detail.php
@brief メンバー詳細（講座IDをURLパラメータで指定可能、video_urlを表示）
*/

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
    public function __construct() {
        parent::__construct();
        global $member_id, $course_id;

        // member_id
        if (isset($_GET['mid']) && cutil::is_number($_GET['mid']) && $_GET['mid'] > 0) {
            $member_id = $_GET['mid'];
        }
        if (isset($_POST['member_id']) && cutil::is_number($_POST['member_id']) && $_POST['member_id'] > 0) {
            $member_id = $_POST['member_id'];
        }

        // 講座IDをURLパラメータから取得（デフォルト1）
        $course_id = 1;
        if (isset($_GET['cid']) && cutil::is_number($_GET['cid']) && $_GET['cid'] > 0) {
            $course_id = $_GET['cid'];
        }

        $GLOBALS['course_id'] = $course_id;
    }

    public function post_default() {
        cutil::post_default("member_name", '');
    }

    public function execute() {
        global $err_array, $err_flag, $course_id;

        // 講座情報取得
        $course_obj = new ccourse();
        $course_data = $course_obj->get_tgt(false, $course_id);

        if (!cutil::array_chk($course_data)) {
            echo '講座が見つかりません。';
            exit;
        }

        $_POST['course_data'] = $course_data;
    }

    public function display() {
        global $member_id;
?>
<link rel="stylesheet" type="text/css" href="css/course_detail.css">
<div class="contents">
<h5><strong>講座詳細</strong></h5>
<form name="form1" action="<?= $_SERVER['PHP_SELF']; ?>" method="post">
<a href="member_list.php">一覧に戻る</a>

<!-- 講座タイトルと説明 -->
<h3><?= htmlspecialchars($_POST['course_data']['course_name'] ?? 'タイトルなし') ?></h3>
<p><?= nl2br(htmlspecialchars($_POST['course_data']['description'] ?? '説明なし')) ?></p>

<!-- 動画埋め込み -->
<?php
$video_url = $_POST['course_data']['video_url'] ?? '';
if ($video_url !== ''):
?>
<iframe width="660" height="415" id="videoFrame" class="mov"
        src="<?= htmlspecialchars($video_url) ?>"
        title="Google Drive video player" frameborder="0"
        allow="autoplay; encrypted-media"
        allowfullscreen></iframe>
<?php else: ?>
<p>動画が登録されていません。</p>
<?php endif; ?>





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

// ページ構築
$page_obj = new cnode();
$page_obj->add_child(cutil::create('cheader'));
$page_obj->add_child($cmain_obj = cutil::create('cmain_node'));
$page_obj->add_child(cutil::create('cfooter'));
$page_obj->create();
$cmain_obj->execute();
$page_obj->display();
?>
