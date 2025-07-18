<?php
/*!
@file contents_node.php
@brief 共有するノード
@copyright Copyright (c) 2024 Yamanoi Yasushi.
*/

////////////////////////////////////


//--------------------------------------------------------------------------------------
///	ヘッダノード
//--------------------------------------------------------------------------------------
class cheader extends cnode {
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
	@brief	構築時の処理(継承して使用)
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	public function create(){
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief  表示(継承して使用)
	@return なし
	*/
	//--------------------------------------------------------------------------------------
	public function display(){
	$echo_str = <<< END_BLOCK
<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PHPBase2サンプル</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
  <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
  <div class="contents"> <!-- sticky footer 用のラッパー -->

    <header class="header">
      <form class="search-bar" action="course_list.php" method="GET">
        <div class="search-box">
          <input type="text" placeholder="検索ワード" name="keyword">
          <button type="submit">
            <i class="fas fa-search fa-fw">🔍</i>
          </button>
        </div>
      </form>

      <div class="right">
        <a href="http://150.95.36.201/~j2025b/admin/admin_login.php" class="logo-button">管理者画面</a>
        <a href="common/login.php" class="logo-button">ロゴ</a>
        <a href="common/login.php" class="icon profile-icon"><img src="./img/アイコン.png" width="50px" ></a>
      </div>
    </header>

    <div class="container"> <!-- ここに各ページのコンテンツが入る -->

END_BLOCK;
	echo $echo_str;
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

//--------------------------------------------------------------------------------------
///	サイドバー付きヘッダノード
//--------------------------------------------------------------------------------------
class cside_header extends cnode {
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
	@brief	構築時の処理(継承して使用)
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	public function create(){
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief  表示(継承して使用)
	@return なし
	*/
	//--------------------------------------------------------------------------------------
	public function display(){
		$echo_str = <<< END_BLOCK

<!doctype html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>サンプルサイト</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
<div class="contents"> <!-- ←追加 -->
<!-- 全体コンテナ　-->
<div class="container-fluid">
<header class="navbar sticky-top bg-secondary-subtle flex-md-nowrap p-0 shadow" >
<a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-6 text-dark" href="#">サイト名</a>

</header>
	<!-- 行　-->
	<div class="row">
		
END_BLOCK;
		echo $echo_str;
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


//--------------------------------------------------------------------------------------
///	フッターノード
//--------------------------------------------------------------------------------------
class cfooter extends cnode {
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
	@brief	構築時の処理(継承して使用)
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	public function create(){
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief  表示(継承して使用)
	@return なし
	*/
	//--------------------------------------------------------------------------------------
	public function display(){
	$echo_str = <<< END_BLOCK
    </div> <!-- .container -->
    
    <footer class="py-3 my-4 border-dark border-top">
      <p class="text-center text-body-secondary">利用規約</p>
      <p class="text-center text-body-secondary">プライバシーポリシー</p>
      <p class="text-center text-body-secondary">会社概要</p>
    </footer>
  </div> <!-- .contents -->

  <div class="b-divider"></div>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
  <script src="js/scripts.js"></script>
</body>
</html>
END_BLOCK;
	echo $echo_str;
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


//--------------------------------------------------------------------------------------
///	サイドバー付きフッターノード
//--------------------------------------------------------------------------------------
class cside_footer extends cnode {
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
	@brief	構築時の処理(継承して使用)
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	public function create(){
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief  表示(継承して使用)
	@return なし
	*/
	//--------------------------------------------------------------------------------------
	public function display(){
		$echo_str = <<< END_BLOCK

		<footer class="py-3 my-4 border-dark border-top">
			<p class="text-center text-body-secondary">&copy; 2024 サイト名</p>
		</footer>
	</div>
	<!--/ 行　-->
</div>
<!-- /全体コンテナ　-->
	<div class="b-divider"></div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js" integrity="sha384-fbbOQedDUMZZ5KreZpsbe1LCZPVmfTnH7ois6mU1QK+m14rQ1l2bGBq41eYeM/fS" crossorigin="anonymous"></script>
<script src="js/scripts.js"></script>
</body>
</html>
END_BLOCK;
		echo $echo_str;
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


//--------------------------------------------------------------------------------------
///	住所ノード
//--------------------------------------------------------------------------------------
class caddress extends cnode {
	public $param_arr;
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コンストラクタ
	*/
	//--------------------------------------------------------------------------------------
	public function __construct($param_arr) {
		$this->param_arr = $param_arr;
		//親クラスのコンストラクタを呼ぶ
		parent::__construct();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	パラメータのチェック
	@return	なし（エラーの場合はエラーフラグを立てる）
	*/
	//--------------------------------------------------------------------------------------
	public function paramchk(){
		global $err_array;
		global $err_flag;
		if($this->param_arr['cntl_header_name'] == 'par' && $_POST['member_minor'] == 0 ){
			//保護者は未成年の時だけ必須
			return;
		}
		/// 名前の存在と空白チェック
		if(cutil_ex::chkset_err_field($err_array,"{$this->param_arr['cntl_header_name']}_name","{$this->param_arr['head']}名",'isset_nl')){
			$err_flag = 1;
		}
		/// 問題チェック
		if(cutil_ex::chkset_err_field($err_array,"{$this->param_arr['cntl_header_name']}_question_id","{$this->param_arr['head']}問題",'isset_num_range',1,47)){
			$err_flag = 1;
		}
		/// 住所の存在と空白チェック
		if(cutil_ex::chkset_err_field($err_array,"{$this->param_arr['cntl_header_name']}_address","{$this->param_arr['head']}市区郡町村以下",'isset_nl')){
			$err_flag = 1;
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
	@brief  POST変数のデフォルト値をセット
	@return なし
	*/
	//--------------------------------------------------------------------------------------
	public function post_default(){
		cutil::post_default("{$this->param_arr['cntl_header_name']}_question_id",0);
		cutil::post_default("{$this->param_arr['cntl_header_name']}_name",'');
		cutil::post_default("{$this->param_arr['cntl_header_name']}_address",'');
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	名前コントロールの取得
	@return	名前コントロール
	*/
	//--------------------------------------------------------------------------------------
	function get_name(){
		global $err_array;
		$ret_str = '';
		$tgt = new ctextbox("{$this->param_arr['cntl_header_name']}_name",
				$_POST["{$this->param_arr['cntl_header_name']}_name"],'size="70"');
		$ret_str = $tgt->get($_POST['func'] == 'conf');
		if(isset($err_array["{$this->param_arr['cntl_header_name']}_name"])){
			$ret_str .=  '<br /><span class="text-danger">' 
			. cutil::ret2br($err_array["{$this->param_arr['cntl_header_name']}_name"]) 
			. '</span>';
		}
		return $ret_str;
	}

	//--------------------------------------------------------------------------------------
	/*!
	@brief	問題プルダウンの取得
	@return	問題プルダウン文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_question_select(){
		global $err_array;
		//問題の一覧を取得
		$question_obj = new cquestion();
		$allcount = $question_obj->get_all_count(false);
		$question_rows = $question_obj->get_all(false,0,$allcount);
		$tgt = new cselect("{$this->param_arr['cntl_header_name']}_question_id");
		$tgt->add(0,'選択してください',$_POST["{$this->param_arr['cntl_header_name']}_question_id"] == 0);
		foreach($question_rows as $key => $val){
			$tgt->add($val['question_id'],$val['question_name'],
			$val['question_id'] == $_POST["{$this->param_arr['cntl_header_name']}_question_id"]);
		}
		$ret_str = $tgt->get($_POST['func'] == 'conf');
		if(isset($err_array["{$this->param_arr['cntl_header_name']}_question_id"])){
			$ret_str .=  '<br /><span class="text-danger">' 
			. cutil::ret2br($err_array["{$this->param_arr['cntl_header_name']}_question_id"]) 
			. '</span>';
		}
		return $ret_str;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	住所の取得
	@return	住所文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_address(){
		global $err_array;
		$tgt = new ctextbox("{$this->param_arr['cntl_header_name']}_address",
				$_POST["{$this->param_arr['cntl_header_name']}_address"],'size="80"');
		$ret_str = $tgt->get($_POST['func'] == 'conf');
		if(isset($err_array["{$this->param_arr['cntl_header_name']}_address"])){
			$ret_str .=  '<br /><span class="text-danger">' 
			. cutil::ret2br($err_array["{$this->param_arr['cntl_header_name']}_address"]) 
			. '</span>';
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
		$name_str = "{$this->param_arr['head']}名";
		$prefec_str = "{$this->param_arr['head']}問題";
		$address_str = "{$this->param_arr['head']}市区郡町村以下";
//PHPブロック終了
?>
<tr>
<th class="text-center"><?= $name_str ?></th>
<td width="70%"><?= $this->get_name(); ?></td>
</tr>
<tr>
<th class="text-center"><?= $prefec_str ?></th>
<td width="70%"><?= $this->get_question_select(); ?></td>
</tr>
<tr>
<th class="text-center"><?= $address_str ?></th>
<td width="70%"><?= $this->get_address(); ?></td>
</tr>
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



