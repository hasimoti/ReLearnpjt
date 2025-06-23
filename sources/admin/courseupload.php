<?php
/*!
@file course_detail.php
@brief 講座詳細
@copyright Copyright (c) 2024 Yamanoi Yasushi.
*/

//ライブラリをインクルード
require_once("../common/libs.php");

$err_array = array();
$err_flag = 0;
$page_obj = null;
//プライマリキー
$course_id = 0;

//--------------------------------------------------------------------------------------
///	本体ノード
//--------------------------------------------------------------------------------------
class cmain_node extends cnode {
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コンストラクタ
	*/
	//--------------------------------------------------------------------------------------
	public function __construct() {
		//親クラスのコンストラクタを呼ぶ
		parent::__construct();
		//プライマリキー
		global $course_id;
		if(isset($_GET['pid']) 
		//cutilクラスのメンバ関数をスタティック呼出
			&& cutil::is_number($_GET['pid'])
			&& $_GET['pid'] > 0){
			$course_id = $_GET['pid'];
		}
		//$_POST優先
		if(isset($_POST['course_id']) 
		//cutilクラスのメンバ関数をスタティック呼出
			&& cutil::is_number($_POST['course_id'])
			&& $_POST['course_id'] > 0){
			$course_id = $_POST['course_id'];
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief  POST変数のデフォルト値をセット
	@return なし
	*/
	//--------------------------------------------------------------------------------------
	public function post_default(){
		cutil::post_default("course_name",'');
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	構築時の処理(継承して使用)
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	public function create(){
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief  本体実行（表示前処理）
	@return なし
	*/
	//--------------------------------------------------------------------------------------
	public function execute(){
		global $err_array;
		global $err_flag;
		global $page_obj;
		//プライマリキー
		global $course_id;
		if(is_null($page_obj)){
			echo 'ページが無効です';
			exit();
		}
		if(isset($_POST['func'])){
			switch($_POST['func']){
				case 'set':
					//パラメータのチェック
					$page_obj->paramchk();
					if($err_flag != 0){
						$_POST['func'] = 'edit';
					}
					else{
						$this->regist();
					}
				case 'conf':
					//パラメータのチェック
					$page_obj->paramchk();
					if($err_flag != 0){
						$_POST['func'] = 'edit';
					}
				break;
				case 'edit':
					//戻るボタン。
				break;
				default:
					//通常はありえない
					echo '原因不明のエラーです。';
					exit;
				break;
			}
		}
		else{
			if($course_id > 0){
				$course_obj = new ccourse();
				//$_POSTにデータを読み込む
				$_POST = $course_obj->get_tgt(false,$course_id);
				if(cutil::array_chk($_POST)){
					//データ取得成功
					$_POST['func'] = 'edit';
				}
				else{
					//データの取得に失敗したので
					//新規ページにリダイレクト
					cutil::redirect_exit($_SERVER['PHP_SELF']);
				}
			}
			else{
				//新規の入力フォーム
				$_POST['func'] = 'new';
			}
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	パラメータのチェック
	@return	エラーの場合はfalseを返す
	*/
	//--------------------------------------------------------------------------------------
	function paramchk(){
		global $err_array;
		global $err_flag;
		/// 講座名の存在と空白チェック
		if(cutil_ex::chkset_err_field($err_array,'course_name','講座名','isset_nl')){
			$err_flag = 1;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	講座の追加／更新。保存後自分自身を再読み込みする。
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function regist(){
		global $course_id;
		$change_obj = new crecord();
		$dataarr = array();
		$dataarr['course_name'] = (string)$_POST['course_name'];
		if($course_id > 0){
			$where = 'course_id = :course_id';
			$wherearr[':course_id'] = (int)$course_id;
			$change_obj->update_core(false,'course',$dataarr,$where,$wherearr,false);
			cutil::redirect_exit($_SERVER['PHP_SELF'] . '?pid=' . $course_id);
		}
		else{
			$pid = $change_obj->insert_core(false,'course',$dataarr,false);
			cutil::redirect_exit($_SERVER['PHP_SELF'] . '?pid=' . $pid);
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	エラー存在文字列の取得
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function get_err_flag(){
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
	@brief	講座IDの取得(新規の場合は「新規」)
	@return	講座ID
	*/
	//--------------------------------------------------------------------------------------
	function get_course_id_txt(){
		global $course_id;
		if($course_id <= 0){
			return '新規';
		}
		else{
			return $course_id;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	講座コントロールの取得
	@return	講座コントロール
	*/
	//--------------------------------------------------------------------------------------
	function get_course_name(){
		global $err_array;
		$ret_str = '';
		$tgt = new ctextbox('course_name',$_POST['course_name'],'size="70"');
		$ret_str = $tgt->get($_POST['func'] == 'conf');
		if(isset($err_array['course_name'])){
			$ret_str .=  '<br /><span class="text-danger">' 
			. cutil::ret2br($err_array['course_name']) 
			. '</span>';
		}
		return $ret_str;
	}

	//--------------------------------------------------------------------------------------
	/*!
	@brief	操作ボタンの取得
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function get_switch(){
		global $course_id;
		$ret_str = '';
		if($_POST['func'] == 'conf'){
			$button = '更新';
			if($course_id <= 0){
				$button = '追加';
			}
			$ret_str =<<<END_BLOCK

<input type="button"  value="戻る" onClick="set_func_form('edit','')"/>&nbsp;
<input type="button"  value="{$button}" onClick="set_func_form('set','')"/>
END_BLOCK;
		}
		else{
			$ret_str =<<<END_BLOCK

<input type="button"  value="確認" onClick="set_func_form('conf','')"/>
END_BLOCK;
		}
		return $ret_str;
	}


	//--------------------------------------------------------------------------------------
	/*!
	@brief  表示(継承して使用)
	@return なし
	*/
	//--------------------------------------------------------------------------------------
	public function display(){
		global $course_id;
//PHPブロック終了
?>
<!-- コンテンツ　-->
 <link rel="stylesheet" href="../css/courseupload.css">
<div class="contents">
<?= $this->get_err_flag(); ?>
<h5><strong>講座詳細</strong></h5>
<form name="form1" action="<?= $_SERVER['PHP_SELF']; ?>" method="post" >
<a href="course_list.php">一覧に戻る</a>
 
<form action="video_save.php" method="post">
  <h2>動画を編集</h2>

<iframe width="660" height="415" id="videoFrame" class="mov" src="https://drive.google.com/file/d/1R1AzXZfQkXbilBmG7XWFtnVwPqOOLgHF/preview" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>

<button type="button" id="reload" >更新</button><br>
  <label>タイトル</label><br>
  <input type="text" name="title" required><br><br>

  <label>カテゴリ</label><br>
  <select name="category">
    <option value="プログラミング">プログラミング</option>
    <option value="デザイン">デザイン</option>
    <option value="ビジネス">ビジネス</option>
  </select><br><br>

  <label>概要</label><br>
  <textarea name="description" rows="4" cols="50" required></textarea><br><br>

  <label>動画リンク（Google Drive共有リンク）</label><br>
  <input type="url" id="urlv" name="video_url" required><br><br>
 <input type="submit" value="保存" class="btn btn-primary">

  <a href="testcreate.php" class="btn btn-secondary">テスト作成</a>

 <!-- 既存のテストを選ぶ -->
  <label for="test_id">関連テスト</label>
  <select name="test_id" id="test_id">
    <option value="">-- テストを選択してください --</option>
   <!-- 
      // テスト一覧をDBから取得（例）
      $pdo = new PDO("mysql:host=localhost;dbname=your_db", "user", "pass");
      $stmt = $pdo->query("SELECT id, name FROM tests");
      while ($row = $stmt->fetch()) {
        echo "<option value='{$row['id']}'>{$row['name']}</option>";
      }
-->
  </select>





 
</form>




<input type="hidden" name="func" value="" />
<input type="hidden" name="param" value="" />
<input type="hidden" name="course_id" value="<?= $course_id; ?>" />
<p class="text-center"><?= $this->get_switch(); ?></p>
</form>
</div>



  <h2>動画アップロード</h2>
<form id="uploadForm">
  <input type="file" id="fileInput" name="file" accept="video/*" required />
  <button type="submit">アップロード</button>
</form>
<p id="result"></p>



<script>

    const movup = () => 
{
    const iframe = document.getElementById('videoFrame'); // ← ここで取得

    let url = document.getElementById('urlv').value;

    if (url.includes("view?usp=sharing")) {
      // view?usp=sharing を削除して preview に置き換え
      iframe.src = url.replace("view?usp=sharing", "preview");
    } else {
      iframe.src = url;
    }

}
const seikinsan = () => 
{
    const iframe = document.getElementById('videoFrame'); // ← ここで取得
    iframe.src = "https://www.youtube.com/embed/uGLVtWojYiQ?si=G37T6p29PhgjqCly";
}



 document.getElementById('reloada').addEventListener('click',seikinsan);
document.getElementById('reload').addEventListener('click',movup);



 document.getElementById('uploadForm').addEventListener('submit', async function (e) {
  e.preventDefault();
  const file = document.getElementById('fileInput').files[0];
  const formData = new FormData();
  formData.append('file', file);

  const response = await fetch('https://script.google.com/macros/s/AKfycbyvVQ7R8S17EtW1tQijCiTgyZ_Dffy49UCwd4dFRRx1/dev', {
    method: 'POST',
    body: formData,
  });

  const result = await response.text();
  document.getElementById('result').innerText = result;
});



    </script>
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
$page_obj->add_child($main_obj = cutil::create('cmain_node'));
//フッタ追加
$page_obj->add_child(cutil::create('cfooter'));
//構築時処理
$page_obj->create();
//本体実行（表示前処理）
$main_obj->execute();
//ページ全体を表示
$page_obj->display();

?>