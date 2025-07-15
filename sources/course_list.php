<?php
/*!
@file course_list.php
@brief 講座一覧
@copyright Copyright (c) 2024 Yamanoi Yasushi.
*/

//ライブラリをインクルード
require_once("common/libs.php");
require_once("common/course.php");


$err_array = array();
$err_flag = 0;
$page_obj = null;
//プライマリキー
$member_id = 0;

//--------------------------------------------------------------------------------------
///	本体ノード
//--------------------------------------------------------------------------------------
class cmain_node extends cnode {
    //--------------------------------------------------------------------------------------
    /*!
    @brief	コンストラクタ
    */
    //--------------------------------------------------------------------------------------

	public function execute() {
        // 必要な場合はここに処理を追加
    }
    public function __construct() {
        //親クラスのコンストラクタを呼ぶ
        parent::__construct();
        //プライマリキー
        global $member_id;
        if(isset($_GET['mid']) 
        //cutilクラスのメンバ関数をスタティック呼出
            && cutil::is_number($_GET['mid'])
            && $_GET['mid'] > 0){
            $member_id = $_GET['mid'];
        }
        //$_POST優先
        if(isset($_POST['member_id']) 
        //cutilクラスのメンバ関数をスタティック呼出
            && cutil::is_number($_POST['member_id'])
            && $_POST['member_id'] > 0){
            $member_id = $_POST['member_id'];
        }
    }

	 //--------------------------------------------------------------------------------------
    /*!
    @brief	エラー存在文字列の取得
    @return	なし
    */
    //--------------------------------------------------------------------------------------
    public function get_err_flag(){
        global $err_flag;
        switch($err_flag){
            case 1:
            $str =<<<END_BLOCK

<p class="text-danger">入力エラーがあります。各項目のエラーを確認してください。</p>
END_BLOCK;

return $str;
            break;
            case 2:
            $str =<<<END_BLOCK

<p class="text-danger">更新に失敗しました。サポートを確認下さい。</p>
END_BLOCK;
            return $str;
            break;
        }
        return '';
    }

    //--------------------------------------------------------------------------------------
    /*!
    @brief  表示(継承して使用)
    @return なし
    */
    //--------------------------------------------------------------------------------------
    public function display(){
        global $member_id;
//PHPブロック終了
?>
<!-- コンテンツ　-->
 <link rel="stylesheet" type="text/css" href="css/style.css">
<div class="contents">
<?= $this->get_err_flag(); ?>
<h5><strong>講座一覧</strong></h5>
<a href="member_list.php">一覧に戻る</a>

<div class="Thumbnails">
<?php
// 講座一覧を取得して表示
$course_obj = new ccourse();
$courses = $course_obj->get_all(false);
foreach($courses as $course){
    echo '<div class="Thumbnail1">';
    echo '<p>' . htmlspecialchars($course['course_name']) . '</p>';
    if(!empty($course['video_url'])){
        // Google DriveやYouTubeのURLをiframeで埋め込み
        if(strpos($course['video_url'], 'drive.google.com') !== false){
            // Google Drive
            $embed_url = preg_replace('/\/view\?usp=sharing$/', '/preview', $course['video_url']);
            echo '<iframe width="400" height="225" src="' . htmlspecialchars($embed_url) . '" frameborder="0" allowfullscreen></iframe>';
        }else if(strpos($course['video_url'], 'youtube.com') !== false || strpos($course['video_url'], 'youtu.be') !== false){
            // YouTube
            if(strpos($course['video_url'], 'watch?v=') !== false){
                $video_id = explode('watch?v=', $course['video_url'])[1];
            }else{
                $video_id = basename($course['video_url']);
            }
            echo '<iframe width="400" height="225" src="https://www.youtube.com/embed/' . htmlspecialchars($video_id) . '" frameborder="0" allowfullscreen></iframe>';
        }else{
            // その他の動画URL
            echo '<video width="400" controls><source src="' . htmlspecialchars($course['video_url']) . '"></video>';
        }
    }
    echo '<p>' . htmlspecialchars($course['description']) . '</p>';
    echo '</div>';
}
?>
</div>

<div class="pagination" id="pagination">
  <!-- JavaScriptで描画されます -->
</div>
<script>
  const totalPages = 3;
  let currentPage = 1;

  function renderPagination() {
    const container = document.getElementById('pagination');
    container.innerHTML = '';

    const createLink = (text, page, isDisabled = false) => {
      const link = document.createElement('a');
      link.textContent = text;
      if (!isDisabled) {
        link.addEventListener('click', () => {
          currentPage = page;
          renderPagination();
        });
      } else {
        link.classList.add('disabled');
      }
      return link;
    };

    function createSeparator() {
      const sep = document.createElement('span');
      sep.className = 'separator';
      sep.textContent = '|';
      return sep;
    }

    // ＜前
    container.appendChild(createLink('＜前', currentPage - 1, currentPage === 1));
    container.appendChild(createSeparator());

    // ページ番号
    for (let i = 1; i <= totalPages; i++) {
      const link = createLink(i, i);
      if (i === currentPage) {
        link.classList.add('active');
      }
      container.appendChild(link);
      if (i < totalPages) {
        container.appendChild(createSeparator());
      }
    }

    // ＞次
    container.appendChild(createSeparator());
    container.appendChild(createLink('次＞', currentPage + 1, currentPage === totalPages));
  }

  renderPagination();
</script>
</div>
<!-- /コンテンツ　-->
<?php 
//PHPブロック再開
    }
    //--------------------------------------------------------------------------------------
    /*!
    @brief	デストラクタ
    */
    //--------------------------------------------------------------------------------------
    public function __destruct(){
        //親クラスのデストラクタを呼ぶ
        parent::__destruct();
    }
}

//ページを作成
$page_obj = new cnode();
//ヘッダ追加
$page_obj->add_child(cutil::create('cheader'));
//本体追加
$page_obj->add_child($cmain_obj = cutil::create('cmain_node'));
//フッタ追加
$page_obj->add_child(cutil::create('cfooter'));
//構築時処理
$page_obj->create();
//本体実行（表示前処理）
$cmain_obj->execute();
//ページ全体を表示
$page_obj->display();

?>