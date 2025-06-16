<?php
/*!
@file admin_master_list.php
@brief 管理者一覧
@copyright Copyright (c) 2024 Yamanoi Yasushi.
*/

//ライブラリをインクルード
require_once("../common/libs.php");
//以下はセッション管理用のインクルード
require_once("../common/auth_adm.php");

$err_array = array();
$err_flag = 0;
$page_obj = null;

$admin_master_rows = array();


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
	@brief	データ読み込み
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function readdata(){
		global $admin_master_rows;
		$obj = new cadmin_master();
		$admin_master_rows = $obj->get_all(false);
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	削除
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function deljob(){
		if(isset($_POST['param']) && $_POST['param'] > 0){
			$where = 'admin_master_id = :admin_master_id';
			$wherearr[':admin_master_id'] = (int)$_POST['param'];
			$change_obj = new crecord();
			$change_obj->delete_core(false,'admin_master',$where,$wherearr,false);
		}
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
			return;
		}
		if(isset($_POST['func'])){
			switch($_POST['func']){
				case "del":
					//削除操作
					$this->deljob();
					//再読み込みのためにリダイレクト
					cutil::redirect_exit($_SERVER['PHP_SELF']);
				break;
				default:
					echo 'エラー';
					exit();
				break;
			}
		}
		//データの読み込み
		$this->readdata();
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
	@brief	管理者のリストを得る
	@return	管理者リストの文字列
	*/
	//--------------------------------------------------------------------------------------
	public function get_admin_master_rows(){
		global $admin_master_rows;
		$retstr = '';
		$rowscount = 1;
		if(count($admin_master_rows) > 0){
			foreach($admin_master_rows as $key => $value){
				$javamsg =  '【' . $value['admin_name'] . '】';
				$str =<<<END_BLOCK
<tr>
<td width="20%" class="text-center">
{$value['admin_master_id']}
</td>
<td width="65%" class="text-center">
<a href="admin_master_detail.php?aid={$value['admin_master_id']}">{$value['admin_name']}</a>
</td>
<td width="15%" class="text-center">
<input type="button" value="削除確認" onClick="del_func_form({$value['admin_master_id']},'{$javamsg}');" />
</td>
</tr>
END_BLOCK;
			$retstr .= $str;
			$rowscount++;
			}
		}
		else{
			$retstr =<<<END_BLOCK
<tr><td colspan="3" class="nobottom">管理者が見つかりません</td></tr>
END_BLOCK;
		}
		return $retstr;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	ページャーを得る
	@return	ページャー文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_page_block(){
		global $limit;
		global $page;
		$obj = new cadmin_master();
		$allcount = $obj->get_all_count(false);
		$ctl = new cpager($_SERVER['PHP_SELF'],$allcount,$limit);
		return $ctl->get('page',$page);
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	POSTするURLを得る
	@return	POSTするURL
	*/
	//--------------------------------------------------------------------------------------
	function get_tgt_uri(){
		global $page;
		return $_SERVER['PHP_SELF'] 
		. '?page=' . $page;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief  表示(継承して使用)
	@return なし
	*/
	//--------------------------------------------------------------------------------------
	public function display(){
//PHPブロック終了
?>
<!-- コンテンツ　-->
<div class="contents">
<h5><strong>管理者一覧</strong></h5>
<form name="form1" action="<?= $this->get_tgt_uri(); ?>" method="post" >
<p><a href="admin_master_detail.php">新規</a></p>
<table class="table table-bordered">
<tr>
<th class="text-center">管理者ID</th>
<th class="text-center">管理者名</th>
<th class="text-center">操作</th>
</tr>
<?= $this->get_admin_master_rows(); ?>
</table>
<input type="hidden" name="func" value="" >
<input type="hidden" name="param" value="" >
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
