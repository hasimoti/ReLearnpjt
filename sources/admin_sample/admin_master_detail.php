<?php
/*!
@file admin_master_detail.php
@brief 管理者詳細
@copyright Copyright (c) 2024 Yamanoi Yasushi.
*/

//ライブラリをインクルード
require_once("../common/libs.php");
//以下はセッション管理用のインクルード
require_once("../common/auth_adm.php");

$err_array = array();
$err_flag = 0;
$page_obj = null;
//プライマリキー
$admin_master_id = 0;




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
		global $admin_master_id;
		if(isset($_GET['aid']) 
		//cutilクラスのメンバ関数をスタティック呼出
			&& cutil::is_number($_GET['aid'])
			&& $_GET['aid'] > 0){
			$admin_master_id = $_GET['aid'];
		}
		//$_POST優先
		if(isset($_POST['admin_master_id']) 
		//cutilクラスのメンバ関数をスタティック呼出
			&& cutil::is_number($_POST['admin_master_id'])
			&& $_POST['admin_master_id'] > 0){
			$admin_master_id = $_POST['admin_master_id'];
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief  POST変数のデフォルト値をセット
	@return なし
	*/
	//--------------------------------------------------------------------------------------
	public function post_default(){
		cutil::post_default("admin_name",'');
		cutil::post_default("admin_login",'');
		cutil::post_default("enc_password",'');
		cutil::post_default("enc_password_chk",'');
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
		global $admin_master_id;
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
			if($admin_master_id > 0){
				$admin_master_obj = new cadmin_master();
				//$_POSTにデータを読み込む
				$_POST = $admin_master_obj->get_tgt(false,$admin_master_id);
				if(cutil::array_chk($_POST)){
					//パスワードは表示しない
					$_POST['enc_password'] = '';
					$_POST['enc_password_chk'] = '';
					//データ取得成功
					$_POST['func'] = 'edit';
				}
				else{
					//データの取得に失敗したので新規の入力フォーム
					$_POST['func'] = 'new';
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
		global $admin_master_id;
////////////////////////////////////////////////////////////
		if(cutil_ex::chkset_err_field($err_array,'admin_login','ログイン','isset_nl')){
			$err_flag = 1;
		}
		//管理者ログインのユニークチェック
		$admin_master_obj = new cadmin_master();
		//chk_arrにデータを読み込む
		$chk_arr = $admin_master_obj->get_tgt_login(false,$_POST['admin_login']);
		if($admin_master_id == 0){
			//新規の時
			if(cutil::array_chk($chk_arr)){
				//すでにそのログイン名がある
				$err_array['admin_login'] = "すでに、{$_POST['admin_login']}、は使われています";
				$err_flag = 1;
			}
		}
		else{
			if($chk_arr['admin_master_id'] != $admin_master_id){
				//自分以外のアカウントが使用している
				$err_array['admin_login'] = "すでに、{$_POST['admin_login']}、は使われています";
				$err_flag = 1;
			}
		}
////////////////////////////////////////////////////////////
		//新規の時
		if($admin_master_id == 0){
			if(cutil_ex::chkset_err_field($err_array,'enc_password','パスワード','isset_pass')){
				$err_flag = 1;
			}
			if(cutil_ex::chkset_err_field($err_array,'enc_password_chk','パスワード確認','isset_pass')){
				$err_flag = 1;
			}
			else if($_POST['enc_password'] != $_POST['enc_password_chk']){
				$err_array['enc_password_chk'] = "「パスワード確認」が「パスワード」と違っています。";
				$err_flag = 1;
			}
		}
		//更新の時
		else{
			//パスワードに入力があった
			if($_POST['enc_password'] != ''){
				if(cutil_ex::chkset_err_field($err_array,'enc_password','パスワード','isset_pass')){
					$err_flag = 1;
				}
				if(cutil_ex::chkset_err_field($err_array,'enc_password_chk','パスワード確認','isset_pass')){
					$err_flag = 1;
				}
				else if($_POST['enc_password'] != $_POST['enc_password_chk']){
					$err_array['enc_password_chk'] = "「パスワード確認」が「パスワード」と違っています。";
					$err_flag = 1;
				}
			}
		}
////////////////////////////////////////////////////////////
		/// 管理者の存在と空白チェック
		if(cutil_ex::chkset_err_field($err_array,'admin_name','管理者名','isset_nl')){
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
		global $admin_master_id;
		$change_obj = new crecord();
		$dataarr = array();
	    $dataarr['admin_login'] = (string)$_POST['admin_login'];
	    //パスワードが変更さえているかを確認する
	    if($admin_master_id > 0){
			if($_POST['enc_password'] != ''){
			//パスワードに入力があった（変更された）
				$dataarr['enc_password'] = cutil::pw_encode($_POST['enc_password']);
			}
	    }
	    else{
	        //新規
	        $dataarr['enc_password'] = cutil::pw_encode($_POST['enc_password']);
	    }
	    $dataarr['admin_name'] = (string)$_POST['admin_name'];
		if($admin_master_id > 0){
			$where = 'admin_master_id = :admin_master_id';
			$wherearr[':admin_master_id'] = (int)$admin_master_id;
			$change_obj->update_core(false,'admin_master',$dataarr,$where,$wherearr,false);
			cutil::redirect_exit($_SERVER['PHP_SELF'] . '?aid=' . $admin_master_id);
		}
		else{
			$aid = $change_obj->insert_core(false,'admin_master',$dataarr,false);
			cutil::redirect_exit($_SERVER['PHP_SELF'] . '?aid=' . $aid);
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
	@brief	管理者IDの取得(新規の場合は「新規」)
	@return	管理者ID
	*/
	//--------------------------------------------------------------------------------------
	function get_admin_master_id_txt(){
		global $admin_master_id;
		if($admin_master_id <= 0){
			return '新規';
		}
		else{
			return $admin_master_id;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	管理者名コントロールの取得
	@return	管理者名コントロール
	*/
	//--------------------------------------------------------------------------------------
	function get_admin_name(){
		global $err_array;
		$ret_str = '';
		$tgt = new ctextbox('admin_name',$_POST['admin_name'],'size="70"');
		$ret_str = $tgt->get($_POST['func'] == 'conf');
		if(isset($err_array['admin_name'])){
			$ret_str .=  '<br /><span class="text-danger">' 
			. cutil::ret2br($err_array['admin_name']) 
			. '</span>';
		}
		return $ret_str;
	}

	//--------------------------------------------------------------------------------------
	/*!
	@brief	管理者ログインコントロールの取得
	@return	管理者ログインコントロール
	*/
	//--------------------------------------------------------------------------------------
	function get_admin_login(){
		global $err_array;
		$ret_str = '';
		$tgt = new ctextbox('admin_login',$_POST['admin_login'],'size="70"');
		$ret_str = $tgt->get($_POST['func'] == 'conf');
		if(isset($err_array['admin_login'])){
			$ret_str .=  '<br /><span class="text-danger">' 
			. cutil::ret2br($err_array['admin_login']) 
			. '</span>';
		}
		return $ret_str;
	}

	//--------------------------------------------------------------------------------------
	/*!
	@brief	パスワードコントロールの取得
	@return	メパスワードコントロール
	*/
	//--------------------------------------------------------------------------------------
	function get_enc_password(){
		global $err_array;
		$ret_str = '';
		$tgt = new cpasswordtextbox('enc_password',$_POST['enc_password'],'*','size="70"');
		$ret_str = $tgt->get($_POST['func'] == 'conf');
		if(isset($err_array['enc_password'])){
			$ret_str .=  '<br /><span class="text-danger">' 
			. cutil::ret2br($err_array['enc_password']) 
			. '</span>';
		}
		return $ret_str;
	}

	//--------------------------------------------------------------------------------------
	/*!
	@brief	パスワードチェックコントロールの取得
	@return	メパスワードチェックコントロール
	*/
	//--------------------------------------------------------------------------------------
	function get_enc_password_chk(){

		global $err_array;
		$ret_str = '';
		$tgt = new cpasswordtextbox('enc_password_chk',$_POST['enc_password_chk'],'*','size="70"');
		$ret_str = $tgt->get($_POST['func'] == 'conf');
		if(isset($err_array['enc_password_chk'])){
			$ret_str .=  '<br /><span class="text-danger">' 
			. cutil::ret2br($err_array['enc_password_chk']) 
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
		global $admin_master_id;
		$ret_str = '';
		if($_POST['func'] == 'conf'){
			$button = '更新';
			if($admin_master_id <= 0){
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
		global $admin_master_id;
//PHPブロック終了
?>
<!-- コンテンツ　-->
<div class="contents">
<?= $this->get_err_flag(); ?>
<h5><strong>管理者詳細</strong></h5>
<form name="form1" action="<?= $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
<a href="admin_master_list.php">一覧に戻る</a>
<table class="table table-bordered">
<tr>
<th class="text-center">ID</th>
<td width="70%"><?= $this->get_admin_master_id_txt(); ?></td>
</tr>
<tr>
<th class="text-center">管理者名</th>
<td width="70%"><?= $this->get_admin_name(); ?></td>
</tr>
<tr>
<th class="text-center">管理者ログイン</th>
<td width="70%"><?= $this->get_admin_login(); ?></td>
</tr>
<tr>
<th class="text-center">パスワード（変更するとき入力）</th>
<td width="70%"><?= $this->get_enc_password(); ?></td>
</tr>
<tr>
<th class="text-center">パスワード確認（変更するとき入力）</th>
<td width="70%"><?= $this->get_enc_password_chk(); ?></td>
</tr>
</table>
<input type="hidden" name="func" value="" />
<input type="hidden" name="param" value="" />
<input type="hidden" name="admin_master_id" value="<?= $admin_master_id; ?>" />
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
