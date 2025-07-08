<?php
/*!
@file question_detail.php
@brief 問題詳細
@copyright Copyright (c) 2024 Yamanoi Yasushi.
*/

//ライブラリをインクルード
require_once("../common/libs.php");
// DB接続
try {
    $pdo = new PDO('mysql:host=localhost;dbname=j2025bdb;charset=utf8', 'j2025bdb', '9yafMZ9YCfg1S16k!');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB接続エラー: " . $e->getMessage());
}

$test_list = [];

// テスト一覧を取得
global $DB_PDO;

$stmt = $DB_PDO->prepare("SELECT * FROM tests ORDER BY test_id ASC");
$stmt->execute();
$tests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// JSに渡すためにJSON化
echo "<script>const testsFromDB = " . json_encode($tests) . ";</script>";



$error = '';

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

		// ✅ ここから下に「問題＋選択肢 or 記述式」の保存処理を書く

	// 問題は複数あるのでループ処理で
	for ($i = 1; isset($_POST["question_$i"]); $i++) {
		$question_text = $_POST["question_$i"];
		$type = $_POST["type_$i"];
		$explain = $_POST["explain_$i"];

		// 問題本体をINSERT
		$question_data = array(
			'question_text' => $question_text,
			'type' => $type,
			'explanation' => $explain,
			'test_id' => $question_id
		);
		$question_obj = new crecord();
		$qid = $question_obj->insert_core(false, 'questions', $question_data, false);

		if ($type === 'choice') {
			$correct = $_POST["answer_$i"];
			for ($j = 1; isset($_POST["choice_{$i}_$j"]); $j++) {
				$choice_data = array(
					'question_id' => $qid,
					'choice_text' => $_POST["choice_{$i}_$j"],
					'is_correct' => ($correct == $j ? 1 : 0)
				);
				$choice_obj = new crecord();
				$choice_obj->insert_core(false, 'choices', $choice_data, false);
			}
		} elseif ($type === 'text') {
			$text_data = array(
				'question_id' => $qid,
				'answer_text' => $_POST["answer_text_$i"]
			);
			$txt_obj = new crecord();
			$txt_obj->insert_core(false, 'text_answers', $text_data, false);
		}
	}

	// 完了後リダイレクト
	cutil::redirect_exit($_SERVER['PHP_SELF'] . '?pid=' . $question_id);
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
<div class="container">
	<?= $this->get_err_flag(); ?>
	<h5><strong>問題詳細</strong></h5>
	<form name="form1" action="<?= $_SERVER['PHP_SELF']; ?>" method="post" >
		<a href="question_list.php">一覧に戻る</a>


  		<div class="container">
			<aside class="sidebar">
				<!-- サイドバー -->
				<h3>テスト一覧</h3>
				<ul id="testList">
					<li class="test-item active" onclick="selectTest(this)">入社初日マナーテスト</li>
				</ul>

				<div id="newCategoryArea"></div>
				<li class="add-category" onclick="showNewCategoryInput()">＋ 新規カテゴリ</li>

				<div class="sidebar-footer">
					<button class="save-btn" onclick="saveAllTests()">保存</button>
					<button class="delete-btn">削除</button>
				</div>
			</aside>

			<main class="content">
				<h2>テスト作成</h2>
				<form name="form1" action="<?= $_SERVER['PHP_SELF']; ?>" method="post">
					<div class="form">
						<div class="question-list" id="questionList">
						<!-- 問題ブロックがJSで追加される -->
						</div>

						<button type="button" class="add-question" onclick="addQuestion()">＋ 問題を追加</button>

						<div class="button-group">
							<button type="submit">下書き保存</button>
							<button type="button" class="preview">プレビュー</button>
							<button type="button" class="publish">公開する</button>
						</div>
					</div>

					<input type="hidden" name="func" value="" />
					<input type="hidden" name="param" value="" />
					<input type="hidden" name="question_id" value="<?= $question_id; ?>" />
					<p class="text-center"><?= $this->get_switch(); ?></p>
				</form>
			</main>
		</div>
</div>



<input type="hidden" name="func" value="" />
<input type="hidden" name="param" value="" />
<input type="hidden" name="question_id" value="<?= $question_id; ?>" />
<p class="text-center"><?= $this->get_switch(); ?></p>
</form>


<?php
// データ取得（test_idベースなどに応じて変更）仮に$question_idで取得
if ($question_id > 0) {
    $pdo = cdb::connect();
    $sql = "SELECT * FROM questions WHERE test_id = :test_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':test_id', $question_id, PDO::PARAM_INT);
    $stmt->execute();
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 各質問に対して選択肢を取得
    foreach ($questions as &$q) {
        if ($q['type'] === 'choice') {
            $sql2 = "SELECT * FROM choices WHERE question_id = :qid";
            $stmt2 = $pdo->prepare($sql2);
            $stmt2->bindValue(':qid', $q['id'], PDO::PARAM_INT);
            $stmt2->execute();
            $q['choices'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        } elseif ($q['type'] === 'text') {
            $sql3 = "SELECT * FROM text_answers WHERE question_id = :qid";
            $stmt3 = $pdo->prepare($sql3);
            $stmt3->bindValue(':qid', $q['id'], PDO::PARAM_INT);
            $stmt3->execute();
            $q['text_answer'] = $stmt3->fetch(PDO::FETCH_ASSOC);
        }
    }

    // JSに渡すためにJSONに変換
    echo "<script>const loadedQuestions = " . json_encode($questions) . ";</script>";
} else {
    echo "<script>const loadedQuestions = [];</script>";
}
?>




<script>
let questionCount = 0;
let testIdCounter = 1;
let selectedTestElement = null;


function showNewCategoryInput() {
// 右側のテスト作成エリアで新規入力
  selectedTestElement = null;

  // テスト作成エリアに新規入力フォームを表示
  document.querySelector(".form").innerHTML = `
    <h2>新しいテストの作成</h2>

    <label>テスト名</label>
    <input type="text" id="newTestName" placeholder="新しいテスト名を入力"><br>

    <label>制限時間</label>
    <select id="newLimit">
      <option>5分</option>
      <option selected>10分</option>
      <option>15分</option>
    </select><br>

    <div class="question-list" id="questionList">
      <!-- ここにJSで問題が追加される -->
    </div>

    <button type="button" class="add-question" onclick="addQuestion()">＋ 問題を追加</button>

    <div class="button-group">
      <button onclick="addNewTest()">テスト追加</button>
    </div>
  `;

  questionCount = 0;
  addQuestion(); // 最初の1問
}

//新規テスト作成
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

  // フォーム全体を初期化
  document.querySelector(".form").innerHTML = `
    <h2>${el.textContent} の編集</h2>

    <label>テスト名</label>
    <input type="text" value="${el.textContent}"><br>

    <label>制限時間</label>
    <select>
      <option>5分</option>
      <option selected>10分</option>
      <option>15分</option>
    </select><br>

    <div class="question-list" id="questionList">
      <!-- ここにJSで問題が追加される -->
    </div>

    <button type="button" class="add-question" onclick="addQuestion()">＋ 問題を追加</button>

    <div class="button-group">
      <button type="submit">下書き保存</button>
      <button type="button" class="preview">プレビュー</button>
      <button type="button" class="publish">公開する</button>
    </div>
  `;

  // 既存または1問目を表示
  questionCount = 0;
  addQuestion(); // 初期1問だけ追加、必要ならここでデータをロードして複数出す
}

function saveAllTests() {
  const testTitles = Array.from(document.querySelectorAll(".test-item")).map(el => el.textContent);
  console.log("保存されるテスト一覧：", testTitles);

  // 本来ここで DB への保存処理を行う（Ajaxやフォーム送信など）
  alert("保存しました（仮）");
}

let selectedTest = null;
// ページ読み込み時に1問追加
window.onload = () => {
  if (loadedQuestions.length > 0) {
    loadedQuestions.forEach((q, index) => {
      questionCount++;
      const qNum = questionCount;

      const qWrap = document.createElement('div');
      qWrap.className = 'question-block';
      qWrap.innerHTML = `
        <hr>
        <label>問題${qNum}</label>
        <input type="text" name="question_${qNum}" value="${q.question_text}">

        <label>形式</label>
        <select name="type_${qNum}" onchange="toggleType(this, ${qNum})">
          <option value="choice" ${q.type === 'choice' ? 'selected' : ''}>選択式</option>
          <option value="text" ${q.type === 'text' ? 'selected' : ''}>記述式</option>
        </select>

        <div class="choice-group" id="choice_${qNum}" style="${q.type === 'choice' ? '' : 'display:none'}">
          ${generateChoiceHTMLFromDB(qNum, q.choices || [])}
          <button type="button" onclick="addChoice(${qNum})">+ 選択肢を追加</button>
          <button type="button" onclick="removeChoice(${qNum})">− 選択肢を削除</button>
        </div>

        <div class="text-answer" id="text_${qNum}" style="${q.type === 'text' ? '' : 'display:none'}">
          <label>記述式回答欄（ユーザーが記述）</label>
          <textarea name="answer_text_${qNum}" rows="3">${q.text_answer?.answer_text || ''}</textarea>
        </div>

        <label>解説（任意）</label>
        <input type="text" name="explain_${qNum}" value="${q.explanation || ''}">
      `;

      document.getElementById('questionList').appendChild(qWrap);
      choiceCount[qNum] = (q.choices?.length || 2);
    });
  } else {
    addQuestion(); // 何もなければ1問だけ追加
  }

// ✅ 初期表示では新規カテゴリを入力させる
  showNewCategoryInput();

};

//新規テスト作成
function addQuestion() {
  questionCount++;

  const qWrap = document.createElement('div');
  qWrap.className = 'question-block';
  qWrap.id = `question_block_${questionCount}`; // 削除用にID追加

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
      <div class="choices" id="choices_${questionCount}">
        ${generateChoiceHTML(questionCount, 2)}
      </div>
      <button type="button" onclick="addChoice(${questionCount})">+ 選択肢を追加</button>
      <button type="button" onclick="removeChoice(${questionCount})">− 選択肢を削除</button>
    </div>

    <div class="text-answer" id="text_${questionCount}" style="display: none;">
      <label>記述式回答欄（ユーザーが記述）</label>
      <textarea name="answer_text_${questionCount}" rows="3" placeholder="回答例などを記述（任意）"></textarea>
    </div>

    <label>解説（任意）</label>
    <input type="text" name="explain_${questionCount}" placeholder="解説を入力（任意）">

    <!-- ✅ ここが追加される -->
    <div style="text-align: right; margin-top: 10px;">
      <button type="button" onclick="removeQuestion(${questionCount})" style="color: red;">🗑 問題を削除</button>
    </div>
  `;

  document.getElementById('questionList').appendChild(qWrap);
  choiceCount[questionCount] = 2; // 初期は2択
}

// 問題削除
function removeQuestion(qNum) {
  const block = document.getElementById(`question_block_${qNum}`);
  if (block) {
    block.remove();
  }

  // 全ての問題ブロックを再取得
  const blocks = document.querySelectorAll('.question-block');
  questionCount = blocks.length; // 再カウント

  choiceCount = {}; // リセット

  blocks.forEach((block, index) => {
    const newNum = index + 1;
    block.id = `question_block_${newNum}`;

    // labelとinputの書き換え
    block.querySelectorAll("label").forEach(label => {
      if (label.textContent.startsWith("問題")) {
        label.textContent = `問題${newNum}`;
      }
    });

    // 再設定するinputやselectなど
    const questionInput = block.querySelector(`input[name^="question_"]`);
    if (questionInput) questionInput.name = `question_${newNum}`;

    const typeSelect = block.querySelector(`select[name^="type_"]`);
    if (typeSelect) {
      typeSelect.name = `type_${newNum}`;
      typeSelect.setAttribute("onchange", `toggleType(this, ${newNum})`);
    }

    // 解説
    const explainInput = block.querySelector(`input[name^="explain_"]`);
    if (explainInput) explainInput.name = `explain_${newNum}`;

    // 記述式
    const textDiv = block.querySelector(`[id^="text_"]`);
    if (textDiv) {
      textDiv.id = `text_${newNum}`;
      const textarea = textDiv.querySelector(`textarea[name^="answer_text_"]`);
      if (textarea) textarea.name = `answer_text_${newNum}`;
    }

    // 選択式
    const choiceDiv = block.querySelector(`[id^="choice_"]`);
    if (choiceDiv) {
      choiceDiv.id = `choice_${newNum}`;
      const choicesContainer = block.querySelector(`[id^="choices_"]`);
      if (choicesContainer) choicesContainer.id = `choices_${newNum}`;

      // 選択肢の名前と値をリネーム
      const labels = choicesContainer.querySelectorAll('label');
      labels.forEach((label, i) => {
        const radio = label.querySelector('input[type="radio"]');
        const input = label.querySelector('input[type="text"]');
        if (radio) radio.name = `answer_${newNum}`;
        if (input) input.name = `choice_${newNum}_${i + 1}`;
      });
      choiceCount[newNum] = labels.length;
    }

    // 削除ボタンの関数も更新
    const deleteBtn = block.querySelector('button[onclick^="removeQuestion"]');
    if (deleteBtn) deleteBtn.setAttribute("onclick", `removeQuestion(${newNum})`);
  });
}

function toggleType(selectObj, num) {
  const type = selectObj.value;
  document.getElementById(`choice_${num}`).style.display = (type === 'choice') ? 'block' : 'none';
  document.getElementById(`text_${num}`).style.display = (type === 'text') ? 'block' : 'none';
}

let choiceCount = {};

function generateChoiceHTML(qNum, count) {
  let html = '';
  for (let i = 1; i <= count; i++) {
    html += `
      <label>
        <input type="radio" name="answer_${qNum}" value="${i}">
        <input type="text" name="choice_${qNum}_${i}" placeholder="選択肢${i}">
      </label><br>
    `;
  }
  return html;
}

//選択肢追加
function addChoice(qNum) {
  if (!choiceCount[qNum]) choiceCount[qNum] = 2;
  if (choiceCount[qNum] >= 4) return;

  choiceCount[qNum]++;
  const newChoice = document.createElement('label');
  newChoice.innerHTML = `
    <input type="radio" name="answer_${qNum}" value="${choiceCount[qNum]}">
    <input type="text" name="choice_${qNum}_${choiceCount[qNum]}" placeholder="選択肢${choiceCount[qNum]}">
  `;
  const choiceContainer = document.getElementById(`choices_${qNum}`);
  choiceContainer.appendChild(newChoice);
  choiceContainer.appendChild(document.createElement('br'));
}

function removeChoice(qNum) {
  if (choiceCount[qNum] <= 2) return;

  const choiceContainer = document.getElementById(`choices_${qNum}`);
  const labels = choiceContainer.querySelectorAll('label');
  const brs = choiceContainer.querySelectorAll('br');

  if (labels.length > 0) {
    choiceContainer.removeChild(labels[labels.length - 1]);
    if (brs.length > 0) {
      choiceContainer.removeChild(brs[brs.length - 1]);
    }
    choiceCount[qNum]--;
  }
}

function generateChoiceHTMLFromDB(qNum, choices) {
  let html = '';
  choices.forEach((c, index) => {
    const num = index + 1;
    html += `
      <label>
        <input type="radio" name="answer_${qNum}" value="${num}" ${c.is_correct == 1 ? 'checked' : ''}>
        <input type="text" name="choice_${qNum}_${num}" value="${c.choice_text}" placeholder="選択肢${num}">
      </label><br>
    `;
  });
  return html;
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