<?php
/*!
@file admin_login.php
@brief 管理者ログイン
@copyright Copyright (c) 2024 Yamanoi Yasushi.
*/

//ライブラリをインクルード
require_once("../common/libs.php");

session_start();

$err_array = array();
$err_flag = 0;
$page_obj = null;

$ERR_STR = "";
$admin_master_id = "";
$admin_name = "";


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
	@brief  本体実行（表示前処理）
	@return なし
	*/
	//--------------------------------------------------------------------------------------
	public function execute(){
		global $ERR_STR;
		global $admin_master_id;
		global $admin_name;
		if(isset($_SESSION['tmB2025_adm']['err']) && $_SESSION['tmB2025_adm']['err'] != ""){
		    $ERR_STR = $_SESSION['tmB2025_adm']['err'];
		}
		//このセッションをクリア
		$_SESSION['tmB2025_adm'] = array();

		if(isset($_POST['admin_login']) && isset($_POST['admin_password'])){
		    if($this->chk_admin_login(
		        strip_tags($_POST['admin_login']),
		        strip_tags($_POST['admin_password']))){
		        session_start();
		        $_SESSION['tmB2025_adm']['admin_login'] = strip_tags($_POST['admin_login']);
		        $_SESSION['tmB2025_adm']['admin_master_id'] = $admin_master_id;
		        $_SESSION['tmB2025_adm']['admin_name'] = $admin_name;
		        cutil::redirect_exit("index.php");
		    }
		}
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
	@brief	ログインのチェック
	@return	メンバーID
	*/
	//--------------------------------------------------------------------------------------
	function chk_admin_login($admin_login,$admin_password){
		global $ERR_STR;
		global $admin_master_id;
		global $admin_name;
		$admin = new cadmin_master();
		$row = $admin->get_tgt_login(false,$admin_login);
		if($row === false || !isset($row['admin_master_id'])){
		    $ERR_STR .= "ログイン名が不定です。\n";
		    return false;
		}
		//暗号化によるパスワード認証
		if(!cutil::pw_check($admin_password,$row['enc_password'])){
		    $ERR_STR .= "パスワードが違っています。\n";
		    return false;
		}
		$admin_master_id = $row['admin_master_id'];
		$admin_name = $row['admin_name'];
		return true;
	}

	//--------------------------------------------------------------------------------------
	/*!
	@brief  表示(継承して使用)
	@return なし
	*/
	//--------------------------------------------------------------------------------------
	public function display(){
		global $ERR_STR;
//PHPブロック終了
?>
<!-- コンテンツ　-->
<div class="contents">
<h5><strong>管理者ログイン</strong></h5>
<p class="text-danger"><?= cutil::ret2br($ERR_STR); ?></p>
<form action="<?= $_SERVER['PHP_SELF']; ?>" method="post">
<div class="mb-3">
<label for="admin_login" class="form-label">ログインID</label>
<input type="text" class="form-control" id="admin_login" name="admin_login">
</div>
<div class="mb-3">
<label for="admin_password" class="form-label">パスワード</label>
<input type="password" class="form-control" id="admin_password" name="admin_password">
</div>
<p class="text-center text-body-secondary"><input type="submit" value="ログイン" class="form-control" id="login_button"></p>
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
