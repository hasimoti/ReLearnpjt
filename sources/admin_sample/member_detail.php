<?php
/*!
@file member_detail.php
@brief メンバー詳細
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
$member_id = 0;

$CMS_FILEUP_DIR = "../upimages/";



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
		cutil::post_default("member_login",'');
		cutil::post_default("enc_password",'');
		cutil::post_default("enc_password_chk",'');
		cutil::post_default("main_image",'');
		cutil::post_default("prefecture_id",0);
		cutil::post_default("member_address",'');
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
		global $member_id;

		/// メンバー名の存在と空白チェック
		if(cutil_ex::chkset_err_field($err_array,'member_name','メンバー名','isset_nl')){
			$err_flag = 1;
		}
////////////////////////////////////////////////////////////
		if(cutil_ex::chkset_err_field($err_array,'member_login','ログイン','isset_nl')){
			$err_flag = 1;
		}
		//メンバーログインのユニークチェック
		$member_obj = new cmember();
		//chk_arrにデータを読み込む
		$chk_arr = $member_obj->get_tgt_login(false,$_POST['member_login']);
		if($member_id == 0){
			//新規の時
			if(cutil::array_chk($chk_arr)){
				//すでにそのログイン名がある
				$err_array['member_login'] = "すでに、{$_POST['member_login']}、は使われています";
				$err_flag = 1;
			}
		}
		else{
			if($chk_arr['member_id'] != $member_id){
				//自分以外のアカウントが使用している
				$err_array['member_login'] = "すでに、{$_POST['member_login']}、は使われています";
				$err_flag = 1;
			}
		}
////////////////////////////////////////////////////////////
		//新規の時
		if($member_id == 0){
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
		/// メンバーの都道府県チェック
		if(cutil_ex::chkset_err_field($err_array,'prefecture_id','メンバー都道府県','isset_num_range',1,47)){
			$err_flag = 1;
		}
		/// メンバー住所の存在と空白チェック
		if(cutil_ex::chkset_err_field($err_array,'member_address','メンバー市区郡町村以下','isset_nl')){
			$err_flag = 1;
		}

		//ファイルのアップロード
		//添付されてなくてもエラーは出さない
		$ext_arr = array();
		$ext_arr[] = '.jpg';
		$ext_arr[] = '.png';
		$subdir = '';
		$this->upload('main_image',$ext_arr,$subdir,'main');


	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief  ファイルアップエラーの取得
	@param[in]  $upfile  イメージ変数名
	@return エラー文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_upload_err($upfile){
		$str = '';
	    switch($_FILES[$upfile]['error']){
	        case 1:
	        case 2:
	            $str = "アップロードされたファイルサイズが上限を越えています。\n";
	        break;
	        case 3:
	            $str = "アップロードされたファイルは一部しかアップロードされませんでした。\n";
	        break;
	        case 4:
	            $str = "画像ファイルはアップロードされませんでした\n";
	        break;
	        case 6:
	            $str = "テンポラリフォルダがありません。管理者に連絡して下さい。\n";
	        break;
	        case 7:
	            $str = "テンポラリファイルへのディスク書き込みに失敗しました。管理者に連絡して下さい。\n";
	        break;
	        default:
	            $str = "原因不明のエラーです。管理者に連絡して下さい。\n";
	        break;
	    }
	    return $str;
	}


	//--------------------------------------------------------------------------------------
	/*!
	@brief  ファイルのアップロード
	@param[in]  $imagefile  イメージ変数名
	@param[in]  $pathext  アップロードを許可する拡張子名（ドットも含める。半角小文字で記述）
	@param[in]  $subdir  サブディレクトリ('2017/')など
	@param[in]  $headstr  先頭につける文字列
	@return 成功すればtrue
	*/
	//--------------------------------------------------------------------------------------
	function upload($imagefile,$pathext,$subdir,$headstr){
	    global $err_array;
	    global $CMS_FILEUP_DIR;
	    if(!isset($_FILES[$imagefile]['name']) || $_FILES[$imagefile]['name'] == ""){
	        return false;
	    }
	    if($_FILES[$imagefile]['error'] == 0){
	        $ext_dot_str = strtolower(strrchr($_FILES[$imagefile]['name'],"."));
			$okflg = false;
			foreach($pathext as $key => $val){
				if($ext_dot_str == $val){
					$okflg = true;
					break;
				}
			}
			if(!$okflg){
				$err_array[$imagefile] = "アップロードファイルの種類が許可されてません";
				return false;
			}
	        //保存されるファイル名は目的に合わせて変更する
	        $datastr = $subdir . $headstr . date("YmdHis") . strrchr($_FILES[$imagefile]['name'],".");
	        $uppath = $CMS_FILEUP_DIR . $datastr;
	        if (!move_uploaded_file($_FILES[$imagefile]['tmp_name'],
	            $uppath)) {
	            $err_array[$imagefile] .= "アップロードに失敗しました";
	            return false;
	        }
	        else{
	            chmod($uppath,0646);
	            //アップロードされたファイルをPOST変数に入れる
	            //データベースに登録する場合は、この変数を使う
	            $_POST[$imagefile] = $datastr;
	            return true;
	        }
	    }
	    else{
	        $err_array[$imagefile] = $this->get_upload_err($imagefile);
	        return false;
	    }
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	メンバーの追加／更新。保存後自分自身を再読み込みする。
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function regist(){
		global $member_id;
		$change_obj = new crecord();
		$dataarr = array();
		//パスワードが変更さえているかを確認する
		if($member_id > 0){
			if($_POST['enc_password'] != ''){
			//パスワードに入力があった（変更された）
				$dataarr['enc_password'] = cutil::pw_encode($_POST['enc_password']);
			}
		}
		else{
			//新規（パスワード必須）
			$dataarr['enc_password'] = cutil::pw_encode($_POST['enc_password']);
	    }
	    $dataarr['member_name'] = (string)$_POST['member_name'];
	    $dataarr['member_login'] = (string)$_POST['member_login'];
	    $dataarr['main_image'] = (string)$_POST['main_image'];
	    $dataarr['prefecture_id'] = (int)$_POST['prefecture_id'];
	    $dataarr['member_address'] = (string)$_POST['member_address'];
	    $dataarr['member_comment'] = (string)$_POST['member_comment'];
		if($member_id > 0){
			$where = 'member_id = :member_id';
			$wherearr[':member_id'] = (int)$member_id;
			$change_obj->update_core(false,'member',$dataarr,$where,$wherearr,false);
			cutil::redirect_exit($_SERVER['PHP_SELF'] . '?mid=' . $member_id);
		}
		else{
			$mid = $change_obj->insert_core(false,'member',$dataarr,false);
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
	function get_member_id_txt(){
		global $member_id;
		if($member_id <= 0){
			return '新規';
		}
		else{
			return $member_id;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	メンバー名コントロールの取得
	@return	メンバー名コントロール
	*/
	//--------------------------------------------------------------------------------------
	function get_member_name(){
		global $err_array;
		$ret_str = '';
		$tgt = new ctextbox('member_name',$_POST['member_name'],'size="70"');
		$ret_str = $tgt->get($_POST['func'] == 'conf');
		if(isset($err_array['member_name'])){
			$ret_str .=  '<br /><span class="text-danger">' 
			. cutil::ret2br($err_array['member_name']) 
			. '</span>';
		}
		return $ret_str;
	}

	//--------------------------------------------------------------------------------------
	/*!
	@brief	メンバーログインコントロールの取得
	@return	メンバーログインコントロール
	*/
	//--------------------------------------------------------------------------------------
	function get_member_login(){
		global $err_array;
		$ret_str = '';
		$tgt = new ctextbox('member_login',$_POST['member_login'],'size="70"');
		$ret_str = $tgt->get($_POST['func'] == 'conf');
		if(isset($err_array['member_login'])){
			$ret_str .=  '<br /><span class="text-danger">' 
			. cutil::ret2br($err_array['member_login']) 
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
	@brief	アップロードコントロールの取得
	@return	アップロードコントロール
	*/
	//--------------------------------------------------------------------------------------
	function get_upload_main_image(){
		global $CMS_FILEUP_DIR;
		$ret_str = '';
		if(isset($_POST['main_image']) && $_POST['main_image'] != ''){
			$ret_str = <<< END_BLOCK
<img src="{$CMS_FILEUP_DIR}{$_POST['main_image']}" width="200px" /><br />
<input type="hidden" name="main_image" value="{$_POST['main_image']}" />
<input type="file"  name="main_image" >
END_BLOCK;
		}
		else{
			$ret_str = <<< END_BLOCK
<input type="hidden" name="main_image" value="" />
<input type="file"  name="main_image" >
END_BLOCK;
		}
		return $ret_str;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	メンバー都道府県プルダウンの取得
	@return	都道府県プルダウン文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_prefecture_select(){
		global $err_array;
		//都道府県の一覧を取得
		$prefecture_obj = new cprefecture();
		$allcount = $prefecture_obj->get_all_count(false);
		$prefecture_rows = $prefecture_obj->get_all(false,0,$allcount);
		$tgt = new cselect('prefecture_id');
		$tgt->add(0,'選択してください',$_POST['prefecture_id'] == 0);
		foreach($prefecture_rows as $key => $val){
			$tgt->add($val['prefecture_id'],$val['prefecture_name'],$val['prefecture_id'] == $_POST['prefecture_id']);
		}
		$ret_str = $tgt->get($_POST['func'] == 'conf');
		if(isset($err_array['prefecture_id'])){
			$ret_str .=  '<br /><span class="text-danger">' 
			. cutil::ret2br($err_array['prefecture_id']) 
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
	function get_member_address(){
		global $err_array;
		$tgt = new ctextbox('member_address',$_POST['member_address'],'size="80"');
		$ret_str = $tgt->get($_POST['func'] == 'conf');
		if(isset($err_array['member_address'])){
			$ret_str .=  '<br /><span class="text-danger">' 
			. cutil::ret2br($err_array['member_address']) 
			. '</span>';
		}
		return $ret_str;
	}

	//--------------------------------------------------------------------------------------
	/*!
	@brief	メンバーコメントの取得
	@return	メンバーコメント文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_member_comment(){
		global $err_array;
		$tgt = new ctextarea('member_comment',$_POST['member_comment'],'cols="70" rows="5"');
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
<div class="contents">
<?= $this->get_err_flag(); ?>
<h5><strong>メンバー詳細</strong></h5>
<form name="form1" action="<?= $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
<a href="member_list.php">一覧に戻る</a>
<table class="table table-bordered">
<tr>
<th class="text-center">ID</th>
<td width="70%"><?= $this->get_member_id_txt(); ?></td>
</tr>
<tr>
<th class="text-center">メンバー名</th>
<td width="70%"><?= $this->get_member_name(); ?></td>
</tr>
<tr>
<th class="text-center">メンバーログイン</th>
<td width="70%"><?= $this->get_member_login(); ?></td>
</tr>

<tr>
<th class="text-center">パスワード（変更するとき入力）</th>
<td width="70%"><?= $this->get_enc_password(); ?></td>
</tr>
<tr>
<th class="text-center">パスワード確認（変更するとき入力）</th>
<td width="70%"><?= $this->get_enc_password_chk(); ?></td>
</tr>
<tr>
<th class="text-center">メンバーイメージ</th>
<td width="70%">
<?= $this->get_upload_main_image(); ?>
</td>
</tr>
<tr>
<th class="text-center">メンバー都道府県</th>
<td width="70%"><?= $this->get_prefecture_select(); ?></td>
</tr>
<tr>
<th class="text-center">メンバー市区郡町村以下</th>
<td width="70%"><?= $this->get_member_address(); ?></td>
</tr>
<tr>
<th class="text-center">コメント</th>
<td width="70%"><?= $this->get_member_comment(); ?></td>
</tr>
</table>
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
