<?php
/*!
@file mail_chk.php
@brief メール送信チェック
@copyright Copyright (c) 2024 Yamanoi Yasushi.
*/

//ライブラリをインクルード
require_once("../common/libs.php");
//以下はセッション管理用のインクルード
require_once("../common/auth_adm.php");

$err_array = array();
$err_flag = 0;
$page_obj = null;




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
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief  POST変数のデフォルト値をセット
	@return なし
	*/
	//--------------------------------------------------------------------------------------
	public function post_default(){
		cutil::post_default("mail_from",'');
		cutil::post_default("mail_to",'');
		cutil::post_default("subject",'');
		cutil::post_default("message",'');
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
			$_POST['func'] = 'new';
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
////////////////////////////////////////////////////////////
		/// 送信元の存在と空白チェック
		if(cutil_ex::chkset_err_field($err_array,'mail_from','送信元','isset_mail')){
			$err_flag = 1;
		}
////////////////////////////////////////////////////////////
		/// 送信先の存在と空白チェック
		if(cutil_ex::chkset_err_field($err_array,'mail_to','送信先','isset_mail')){
			$err_flag = 1;
		}
////////////////////////////////////////////////////////////
		/// タイトルの存在と空白チェック
		if(cutil_ex::chkset_err_field($err_array,'subject','タイトル','isset_nl')){
			$err_flag = 1;
		}
////////////////////////////////////////////////////////////
		/// メッセージの存在と空白チェック
		if(cutil_ex::chkset_err_field($err_array,'message','メッセージ','isset_nl')){
			$err_flag = 1;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	管理者の追加／更新。保存後自分自身を再読み込みする。
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function regist(){
		$Subject = $_POST['subject'];
		$Message = $_POST['message'];
		$Headers = "From: " . $_POST['mail_from'] . "\r\n";
		$Headers .= 'Return-Path: ' . $_POST['mail_from'];
		$Message .= "\r\n";
		$Option = "-f {$_POST['mail_from']}";
		$To = $_POST['mail_to'];
//以下デバッグ用
//メール送信は危険なので、十分デバッグしてから実行してください。
/*
		$chk_str = <<< END_BLOCK
From: {$_POST['mail_from']}<br>
To: {$_POST['mail_to']}<br>
Subject: {$_POST['subject']}<br>
Message: {$_POST['message']}<br>
Option: -f {$_POST['mail_from']}<br>
END_BLOCK;
		echo $chk_str;
		exit();
*/
		//メール送信
		mb_send_mail($To, $Subject, $Message, $Headers,$Option);
		cutil::redirect_exit('mail_thanks.php');
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
	@brief	送信元コントロールの取得
	@return	送信元コントロール
	*/
	//--------------------------------------------------------------------------------------
	function get_mail_from(){
		global $err_array;
		$ret_str = '';
		$tgt = new ctextbox('mail_from',$_POST['mail_from'],'size="70"');
		$ret_str = $tgt->get($_POST['func'] == 'conf');
		if(isset($err_array['mail_from'])){
			$ret_str .=  '<br /><span class="text-danger">' 
			. cutil::ret2br($err_array['mail_from']) 
			. '</span>';
		}
		return $ret_str;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	送信先コントロールの取得
	@return	送信先コントロール
	*/
	//--------------------------------------------------------------------------------------
	function get_mail_to(){
		global $err_array;
		$ret_str = '';
		$tgt = new ctextbox('mail_to',$_POST['mail_to'],'size="70"');
		$ret_str = $tgt->get($_POST['func'] == 'conf');
		if(isset($err_array['mail_to'])){
			$ret_str .=  '<br /><span class="text-danger">' 
			. cutil::ret2br($err_array['mail_to']) 
			. '</span>';
		}
		return $ret_str;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	タイトルコントロールの取得
	@return	タイトルコントロール
	*/
	//--------------------------------------------------------------------------------------
	function get_subject(){
		global $err_array;
		$ret_str = '';
		$tgt = new ctextbox('subject',$_POST['subject'],'size="70"');
		$ret_str = $tgt->get($_POST['func'] == 'conf');
		if(isset($err_array['subject'])){
			$ret_str .=  '<br /><span class="text-danger">' 
			. cutil::ret2br($err_array['subject']) 
			. '</span>';
		}
		return $ret_str;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	メッセージコントロールの取得
	@return	メッセージコントロール
	*/
	//--------------------------------------------------------------------------------------
	function get_message(){
		global $err_array;
		$tgt = new ctextarea('message',$_POST['message'],'cols="70" rows="5"');
		$ret_str = $tgt->get($_POST['func'] == 'conf');
		if(isset($err_array['message'])){
			$ret_str .=  '<br /><span class="text-danger">' 
			. cutil::ret2br($err_array['message']) 
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
		$ret_str = '';
		if($_POST['func'] == 'conf'){
			$button = 'メール送信';
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
		global $admin_master_id;
//PHPブロック終了
?>
<!-- コンテンツ　-->
<div class="contents">
<?= $this->get_err_flag(); ?>
<h5><strong>メール送信テスト</strong></h5>
<p class="text-danger">※メール送信はスパムメールの要因にもなる危険なプログラムです。慎重に使用しましょう</p>
<form name="form1" action="<?= $_SERVER['PHP_SELF']; ?>" method="post" >
<table class="table table-bordered">
<tr>
<th class="text-center">送信元</th>
<td width="70%"><?= $this->get_mail_from(); ?></td>
</tr>
<tr>
<th class="text-center">送信先</th>
<td width="70%"><?= $this->get_mail_to(); ?></td>
</tr>
<tr>
<th class="text-center">タイトル</th>
<td width="70%"><?= $this->get_subject(); ?></td>
</tr>
<tr>
<th class="text-center">メッセージ</th>
<td width="70%"><?= $this->get_message(); ?></td>
</tr>
</table>
<input type="hidden" name="func" value="" />
<input type="hidden" name="param" value="" />
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
