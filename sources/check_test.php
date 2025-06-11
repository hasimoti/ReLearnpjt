<?php
/*!
@file member_detail.php
@brief メンバー詳細
@copyright Copyright (c) 2024 Yamanoi Yasushi.
*/

//ライブラリをインクルード
require_once("common/libs.php");

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
	@brief  POST変数のデフォルト値をセット
	@return なし
	*/
	//--------------------------------------------------------------------------------------
	public function post_default(){
		cutil::post_default("member_name",'');
		cutil::post_default("member_prefecture_id",0);
		cutil::post_default("member_address",'');
		cutil::post_default("member_minor",0);
		cutil::post_default("par_name",'');
		cutil::post_default("par_prefecture_id",0);
		cutil::post_default("par_address",'');
		if(!isset($_POST['fruits']))$_POST['fruits'] = array();
		cutil::post_default("member_comment",'');
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
		global $member_id;
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
			if($member_id > 0){
				$member_obj = new cmember();
				//$_POSTにデータを読み込む
				$_POST = $member_obj->get_tgt(false,$member_id);
				if(cutil::array_chk($_POST)){
					//好きな果物を読み込む
					$_POST['fruits'] = $member_obj->get_all_fruits_match(false,$member_id);
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
	@brief	操作ボタンの取得
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function get_switch(){
		global $member_id;
		$ret_str = '';
		if($_POST['func'] == 'conf'){
			$button = '更新';
			if($member_id <= 0){
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
		global $member_id;
//PHPブロック終了
?>
<!-- コンテンツ　-->
 <link rel="stylesheet" type="text/css" href="css/course_detail.css">
<div class="contents">
<?= $this->get_err_flag(); ?>
<h5><strong>確認テスト</strong></h5>
<form name="form1" action="<?= $_SERVER['PHP_SELF']; ?>" method="post" >
<a href="member_list.php">一覧に戻る</a>

<div class="Q1">Q1
    <label><p>C#の変数宣言で正しいものはどれか</p></label><br>
    <form>
        <label>
            <input type="radio" name="choice" value="option1">
            int num = 10;
        </label><br>

        <label>
            <input type="radio" name="choice" value="option2">
            var num = 10:
        </label><br>

        <label>
            <input type="radio" name="choice" value="option3">
            let num = 10;
        </label>
    </form>
</div>

<button type="submit" class="send">回答</button>
<input type="hidden" name="func" value="" />
<input type="hidden" name="param" value="" />
<input type="hidden" name="member_id" value="<?= $member_id; ?>" />
<p class="text-center"><?= $this->get_switch(); ?></p>
</form>
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