<?php
/*!
@file course_detail.php
@brief メンバー詳細
@copyright Copyright (c) 2024 Yamanoi Yasushi.
*/

//ライブラリをインクルード
require_once("common/libs.php");

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
		if(isset($_GET['mid']) 
		//cutilクラスのメンバ関数をスタティック呼出
			&& cutil::is_number($_GET['mid'])
			&& $_GET['mid'] > 0){
			$course_id = $_GET['mid'];
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
		cutil::post_default("course_prefecture_id",0);
		cutil::post_default("course_address",'');
		cutil::post_default("course_minor",0);
		cutil::post_default("par_name",'');
		cutil::post_default("par_prefecture_id",0);
		cutil::post_default("par_address",'');
		if(!isset($_POST['fruits']))$_POST['fruits'] = array();
		cutil::post_default("course_comment",'');
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
					//好きな果物を読み込む
					$_POST['fruits'] = $course_obj->get_all_fruits_match(false,$course_id);
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
		/// メンバー名の存在と空白チェック
		if(cutil_ex::chkset_err_field($err_array,'course_name','メンバー名','isset_nl')){
			$err_flag = 1;
		}
		/// メンバーの都道府県チェック
		if(cutil_ex::chkset_err_field($err_array,'course_prefecture_id','メンバー都道府県','isset_num_range',1,47)){
			$err_flag = 1;
		}
		/// メンバー住所の存在と空白チェック
		if(cutil_ex::chkset_err_field($err_array,'course_address','メンバー市区郡町村以下','isset_nl')){
			$err_flag = 1;
		}
		/// 未成年だった時の保護者住所
		if($_POST['course_minor'] == 1){
			/// 保護者名の存在と空白チェック
			if(cutil_ex::chkset_err_field($err_array,'par_name','保護者名','isset_nl')){
				$err_flag = 1;
			}
			/// 保護者の都道府県チェック
			if(cutil_ex::chkset_err_field($err_array,'par_prefecture_id','保護者都道府県','isset_num_range',1,47)){
				$err_flag = 1;
			}
			/// 保護者住所の存在と空白チェック
			if(cutil_ex::chkset_err_field($err_array,'par_address','保護者市区郡町村以下','isset_nl')){
				$err_flag = 1;
			}
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	フルーツデータの追加／更新
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function regist_fruits($course_id){
		$where = 'course_id = :course_id';
		$wherearr[':course_id'] = (int)$course_id;
		$change_obj = new crecord();
		$change_obj->delete_core(false,'fruits_match',$where,$wherearr,false);
		foreach($_POST['fruits'] as $key => $val){
			$dataarr = array();
			$dataarr['course_id'] = (int)$course_id;
			$dataarr['fruits_id'] = (int)$val;
			$change_obj->insert_core(false,'fruits_match',$dataarr,false);
		}
	}

	//--------------------------------------------------------------------------------------
	/*!
	@brief	メンバーの追加／更新。保存後自分自身を再読み込みする。
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function regist(){
		global $course_id;
		$change_obj = new crecord();
		$dataarr = array();
		$dataarr['course_name'] = (string)$_POST['course_name'];
		$dataarr['course_prefecture_id'] = (int)$_POST['course_prefecture_id'];
		$dataarr['course_address'] = (string)$_POST['course_address'];
		$dataarr['course_minor'] = (int)$_POST['course_minor'];
		$dataarr['par_name'] = (string)$_POST['par_name'];
		$dataarr['par_prefecture_id'] = (int)$_POST['par_prefecture_id'];
		$dataarr['par_address'] = (string)$_POST['par_address'];
		$dataarr['course_comment'] = (string)$_POST['course_comment'];
		if($course_id > 0){
			$where = 'course_id = :course_id';
			$wherearr[':course_id'] = (int)$course_id;
			$change_obj->update_core(false,'course',$dataarr,$where,$wherearr,false);
			$this->regist_fruits($course_id);
			cutil::redirect_exit($_SERVER['PHP_SELF'] . '?mid=' . $course_id);
		}
		else{
			$mid = $change_obj->insert_core(false,'course',$dataarr,false);
			$this->regist_fruits($mid);
			cutil::redirect_exit($_SERVER['PHP_SELF'] . '?mid=' . $mid);
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
	@brief	メンバーIDの取得(新規の場合は「新規」)
	@return	メンバーID
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
	@brief	メンバー名コントロールの取得
	@return	メンバー名コントロール
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
	@brief	メンバー都道府県プルダウンの取得
	@return	都道府県プルダウン文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_course_prefecture_select(){
		global $err_array;
		//都道府県の一覧を取得
		$prefecture_obj = new cprefecture();
		$allcount = $prefecture_obj->get_all_count(false);
		$prefecture_rows = $prefecture_obj->get_all(false,0,$allcount);
		$tgt = new cselect('course_prefecture_id');
		$tgt->add(0,'選択してください',$_POST['course_prefecture_id'] == 0);
		foreach($prefecture_rows as $key => $val){
			$tgt->add($val['prefecture_id'],$val['prefecture_name'],$val['prefecture_id'] == $_POST['course_prefecture_id']);
		}
		$ret_str = $tgt->get($_POST['func'] == 'conf');
		if(isset($err_array['course_prefecture_id'])){
			$ret_str .=  '<br /><span class="text-danger">' 
			. cutil::ret2br($err_array['course_prefecture_id']) 
			. '</span>';
		}
		return $ret_str;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	メンバー住所の取得
	@return	メンバー住所文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_course_address(){
		global $err_array;
		$tgt = new ctextbox('course_address',$_POST['course_address'],'size="80"');
		$ret_str = $tgt->get($_POST['func'] == 'conf');
		if(isset($err_array['course_address'])){
			$ret_str .=  '<br /><span class="text-danger">' 
			. cutil::ret2br($err_array['course_address']) 
			. '</span>';
		}
		return $ret_str;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	メンバー未成年ラジオボタンの取得
	@return	メンバー未成年ラジオボタン文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_course_minor_radio(){
		global $err_array;
		//メンバー性別のラジオボタンを作成
		$tgt = new cradio('course_minor');
		$tgt->add(0,'成人',$_POST['course_minor'] == 0);
		$tgt->add(1,'未成年',$_POST['course_minor'] == 1);
		$ret_str = $tgt->get($_POST['func'] == 'conf','&nbsp;');
		if(isset($err_array['course_minor'])){
			$ret_str .=  '<br /><span class="text-danger">' 
			. cutil::ret2br($err_array['course_minor']) 
			. '</span>';
		}
		return $ret_str;
	}

	//--------------------------------------------------------------------------------------
	/*!
	@brief	保護者名コントロールの取得
	@return	保護者名コントロール
	*/
	//--------------------------------------------------------------------------------------
	function get_par_name(){
		global $err_array;
		$ret_str = '';
		$tgt = new ctextbox('par_name',$_POST['par_name'],'size="70"');
		$ret_str = $tgt->get($_POST['func'] == 'conf');
		if(isset($err_array['par_name'])){
			$ret_str .=  '<br /><span class="text-danger">' 
			. cutil::ret2br($err_array['par_name']) 
			. '</span>';
		}
		return $ret_str;
	}

	//--------------------------------------------------------------------------------------
	/*!
	@brief	保護者都道府県プルダウンの取得
	@return	保護者都道府県プルダウン文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_par_prefecture_select(){
		global $err_array;
		//都道府県の一覧を取得
		$prefecture_obj = new cprefecture();
		$allcount = $prefecture_obj->get_all_count(false);
		$prefecture_rows = $prefecture_obj->get_all(false,0,$allcount);
		$tgt = new cselect('par_prefecture_id');
		$tgt->add(0,'選択してください',$_POST['par_prefecture_id'] == 0);
		foreach($prefecture_rows as $key => $val){
			$tgt->add($val['prefecture_id'],$val['prefecture_name'],$val['prefecture_id'] == $_POST['par_prefecture_id']);
		}
		$ret_str = $tgt->get($_POST['func'] == 'conf');
		if(isset($err_array['par_prefecture_id'])){
			$ret_str .=  '<br /><span class="text-danger">' 
			. cutil::ret2br($err_array['par_prefecture_id']) 
			. '</span>';
		}
		return $ret_str;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	保護者住所の取得
	@return	保護者住所文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_par_address(){
		global $err_array;
		$tgt = new ctextbox('par_address',$_POST['par_address'],'size="80"');
		$ret_str = $tgt->get($_POST['func'] == 'conf');
		if(isset($err_array['par_address'])){
			$ret_str .=  '<br /><span class="text-danger">' 
			. cutil::ret2br($err_array['par_address']) 
			. '</span>';
		}
		return $ret_str;
	}



	//--------------------------------------------------------------------------------------
	/*!
	@brief	好きな果物チェックボックスの取得
	@return	好きな果物チェックボックス文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_fruits_match_check(){
		global $err_array;
		global $course_id;
		//フルーツの一覧を取得
		$fruits_obj = new cfruits();
		$fruits_rows = $fruits_obj->get_all(false);
		//果物のチェックボックスを作成
		$tgt = new cchkbox('fruits[]');
		if(!isset($_POST['fruits']))$_POST['fruits'] = array();
		foreach($fruits_rows as $key => $val){
			$check = false;
			if(array_search($val['fruits_id'],$_POST['fruits']) !== false){
				$check = true;
			}
			$tgt->add($val['fruits_id'],$val['fruits_name'],$check);
		}
		$ret_str = $tgt->get($_POST['func'] == 'conf','&nbsp;');
		return $ret_str;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	メンバーコメントの取得
	@return	メンバーコメント文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_course_comment(){
		global $err_array;
		$tgt = new ctextarea('course_comment',$_POST['course_comment'],'cols="70" rows="5"');
		$ret_str = $tgt->get($_POST['func'] == 'conf');
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
<div class="contents">
<?= $this->get_err_flag(); ?>
<h5><strong>メンバー詳細</strong></h5>
<form name="form1" action="<?= $_SERVER['PHP_SELF']; ?>" method="post" >
<a href="course_list.php">一覧に戻る</a>
<table class="table table-bordered">
<tr>
<th class="text-center">ID</th>
<td width="70%"><?= $this->get_course_id_txt(); ?></td>
</tr>
<tr>
<th class="text-center">メンバー名</th>
<td width="70%"><?= $this->get_course_name(); ?></td>
</tr>
<tr>
<th class="text-center">メンバー都道府県</th>
<td width="70%"><?= $this->get_course_prefecture_select(); ?></td>
</tr>
<tr>
<th class="text-center">メンバー市区郡町村以下</th>
<td width="70%"><?= $this->get_course_address(); ?></td>
</tr>
<tr>
<tr>
<th class="text-center">好きな果物</th>
<td width="70%"><?= $this->get_fruits_match_check(); ?></td>
</tr>
<th class="text-center">未成年かどうか</th>
<td width="70%"><?= $this->get_course_minor_radio(); ?></td>
</tr>
<tr>
<th class="text-center">保護者名</th>
<td width="70%"><?= $this->get_par_name(); ?></td>
</tr>
<tr>
<th class="text-center">保護者都道府県</th>
<td width="70%"><?= $this->get_par_prefecture_select(); ?></td>
</tr>
<tr>
<th class="text-center">保護者市区郡町村以下</th>
<td width="70%"><?= $this->get_par_address(); ?></td>
</tr>
<tr>
<th class="text-center">コメント</th>
<td width="70%"><?= $this->get_course_comment(); ?></td>
</tr>
</table>
<input type="hidden" name="func" value="" />
<input type="hidden" name="param" value="" />
<input type="hidden" name="course_id" value="<?= $course_id; ?>" />
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
