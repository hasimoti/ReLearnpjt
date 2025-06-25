<?php
/*!
@file question_detail.php
@brief 問題詳細
@copyright Copyright (c) 2024 Yamanoi Yasushi.
*/

//ライブラリをインクルード
require_once("../common/libs.php");

$err_array = array();
$err_flag = 0;
$page_obj = null;
//プライマリキー
$question_id = 0;

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
		global $question_id;
		if(isset($_GET['pid']) 
		//cutilクラスのメンバ関数をスタティック呼出
			&& cutil::is_number($_GET['pid'])
			&& $_GET['pid'] > 0){
			$question_id = $_GET['pid'];
		}
		//$_POST優先
		if(isset($_POST['question_id']) 
		//cutilクラスのメンバ関数をスタティック呼出
			&& cutil::is_number($_POST['question_id'])
			&& $_POST['question_id'] > 0){
			$question_id = $_POST['question_id'];
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief  POST変数のデフォルト値をセット
	@return なし
	*/
	//--------------------------------------------------------------------------------------
	public function post_default(){
		cutil::post_default("question_name",'');
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
		global $question_id;
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
			if($question_id > 0){
				$question_obj = new cquestion();
				//$_POSTにデータを読み込む
				$_POST = $question_obj->get_tgt(false,$question_id);
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
		/// 問題名の存在と空白チェック
		if(cutil_ex::chkset_err_field($err_array,'question_name','問題名','isset_nl')){
			$err_flag = 1;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	問題の追加／更新。保存後自分自身を再読み込みする。
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function regist(){
		global $question_id;
		$change_obj = new crecord();
		$dataarr = array();
		$dataarr['question_name'] = (string)$_POST['question_name'];
		if($question_id > 0){
			$where = 'question_id = :question_id';
			$wherearr[':question_id'] = (int)$question_id;
			$change_obj->update_core(false,'question',$dataarr,$where,$wherearr,false);
			cutil::redirect_exit($_SERVER['PHP_SELF'] . '?pid=' . $question_id);
		}
		else{
			$pid = $change_obj->insert_core(false,'question',$dataarr,false);
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
	@brief	問題IDの取得(新規の場合は「新規」)
	@return	問題ID
	*/
	//--------------------------------------------------------------------------------------
	function get_question_id_txt(){
		global $question_id;
		if($question_id <= 0){
			return '新規';
		}
		else{
			return $question_id;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	問題コントロールの取得
	@return	問題コントロール
	*/
	//--------------------------------------------------------------------------------------
	function get_question_name(){
		global $err_array;
		$ret_str = '';
		$tgt = new ctextbox('question_name',$_POST['question_name'],'size="70"');
		$ret_str = $tgt->get($_POST['func'] == 'conf');
		if(isset($err_array['question_name'])){
			$ret_str .=  '<br /><span class="text-danger">' 
			. cutil::ret2br($err_array['question_name']) 
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
		global $question_id;
		$ret_str = '';
		if($_POST['func'] == 'conf'){
			$button = '更新';
			if($question_id <= 0){
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
		global $question_id;
//PHPブロック終了
?>
<!-- コンテンツ　-->
 <link rel="stylesheet" href="../css/test_create.css">
<div class="contents">
<?= $this->get_err_flag(); ?>
<h5><strong>問題詳細</strong></h5>
<form name="form1" action="<?= $_SERVER['PHP_SELF']; ?>" method="post" >
<a href="question_list.php">一覧に戻る</a>





  <div class="container">
   <aside class="sidebar">
  <h3>テスト一覧</h3>
  <ul id="testList">
    <li class="test-item active" onclick="selectTest(this)">入社初日マナーテスト</li>
  </ul>
  <div id="newCategoryArea"></div>
  <li class="add-category" onclick="showNewCategoryInput()">+ 新規カテゴリ</li>

  <div class="sidebar-footer">
    <button class="save-btn" onclick="saveAllTests()">保存</button>
    <button class="delete-btn">削除</button>
  </div>
</aside>

    <main class="content">
      <h2>テスト作成</h2>
      <div class="form">
      
<!-- JSで生成される問題表示エリア -->
<div class="question-list" id="questionList">
  <!-- 初期で1問だけ表示させる -->
</div>

<!-- 「問題を追加」ボタン -->
<button type="button" class="add-question" onclick="addQuestion()">＋ 問題を追加</button>

  <div class="button-group">
    <button type="submit">下書き保存</button>
    <button type="button" class="preview">プレビュー</button>
    <button type="button" class="publish">公開する</button>
  </div>
       
	</div>
    </main>
  </div>



<input type="hidden" name="func" value="" />
<input type="hidden" name="param" value="" />
<input type="hidden" name="question_id" value="<?= $question_id; ?>" />
<p class="text-center"><?= $this->get_switch(); ?></p>
</form>

<script>
let questionCount = 0;
let testIdCounter = 1;
let selectedTestElement = null;

function showNewCategoryInput() {
  const area = document.getElementById("newCategoryArea");
  area.innerHTML = `
    <input type="text" id="newTestName" placeholder="新しいテスト名を入力">
    <button onclick="addNewTest()">追加</button>
  `;
}

function addNewTest() {
  const name = document.getElementById("newTestName").value.trim();
  if (!name) return;

  const li = document.createElement("li");
  li.className = "test-item";
  li.textContent = name;
  li.onclick = function() { selectTest(li); };

  document.getElementById("testList").appendChild(li);
  document.getElementById("newCategoryArea").innerHTML = "";
}

function selectTest(el) {
  document.querySelectorAll(".test-item").forEach(item => item.classList.remove("active"));
  el.classList.add("active");
  selectedTestElement = el;
  // ここで右側に表示を切り替える
  document.querySelector(".form").innerHTML = `
    <h2>${el.textContent} の編集</h2>
    <label>テスト名</label>
    <input type="text" value="${el.textContent}"><br>
    <!-- 以下省略、選択式や記述式の入力欄など -->
  `;
}

function saveAllTests() {
  const testTitles = Array.from(document.querySelectorAll(".test-item")).map(el => el.textContent);
  console.log("保存されるテスト一覧：", testTitles);

  // 本来ここで DB への保存処理を行う（Ajaxやフォーム送信など）
  alert("保存しました（仮）");
}
// ページ読み込み時に1問追加
window.onload = () => {
  addQuestion();
};

function addQuestion() {
  questionCount++;

  const qWrap = document.createElement('div');
  qWrap.className = 'question-block';
  qWrap.innerHTML = `
    <hr>
    <label>問題${questionCount}</label>
    <input type="text" name="question_${questionCount}" placeholder="問題文を入力してください">

    <label>形式</label>
    <select name="type_${questionCount}" onchange="toggleType(this, ${questionCount})">
      <option value="choice">選択式</option>
      <option value="text">記述式</option>
    </select>

    <div class="choice-group" id="choice_${questionCount}">
      <label><input type="radio" name="answer_${questionCount}" value="1"> <input type="text" name="choice_${questionCount}_1" placeholder="選択肢1"></label><br>
      <label><input type="radio" name="answer_${questionCount}" value="2"> <input type="text" name="choice_${questionCount}_2" placeholder="選択肢2"></label><br>
      <label><input type="radio" name="answer_${questionCount}" value="3"> <input type="text" name="choice_${questionCount}_3" placeholder="選択肢3"></label><br>
      <label><input type="radio" name="answer_${questionCount}" value="4"> <input type="text" name="choice_${questionCount}_4" placeholder="選択肢4"></label>
    </div>

    <div class="text-answer" id="text_${questionCount}" style="display: none;">
      <label>記述式回答欄（ユーザーが記述）</label>
      <textarea name="answer_text_${questionCount}" rows="3" placeholder="回答例などを記述（任意）"></textarea>
    </div>

    <label>解説（任意）</label>
    <input type="text" name="explain_${questionCount}" placeholder="解説を入力（任意）">
  `;

  document.getElementById('questionList').appendChild(qWrap);
}

function toggleType(selectObj, num) {
  const type = selectObj.value;
  document.getElementById(`choice_${num}`).style.display = (type === 'choice') ? 'block' : 'none';
  document.getElementById(`text_${num}`).style.display = (type === 'text') ? 'block' : 'none';
}
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