<?php
/*!
@file index.php
@brief メインメニュー
@copyright Copyright (c) 2024 Yamanoi Yasushi.
*/


session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
//ライブラリをインクルード
require_once("common/libs.php");

 

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


<!-- コンテンツ　-->
<div class="contents">
  <h5><strong>メインメニュー</strong></h5>

<<<<<<< HEAD
<<<<<<< HEAD
  <div class="Thumbnails">
    <div class="Thumbnail1">
      <a href="course_detail2.php?cid=1"><img src="./img/サムネサンプル.jpg" width="400px"></a>  
	  
	  //cidのあとにIDを指定//

      <a href="course_detail2.php?cid=4"><img src="./img/サムネ２.jpg" width="400px"></a>
    </div>
    <div class="Thumbnail2">
      <a href="course_detail2.php?cid=3"><img src="./img/サムネ２.jpg" width="400px"></a>
      <a href="course_detail2.php?cid=2"><img src="./img/サムネ２.jpg" width="400px"></a>
    </div>
  </div>
=======
=======
  <div class="Thumbnails">
    <div class="Thumbnail1">
      <a href="course_list.php"><img src="./img/サムネサンプル.jpg" width="400px"></a>
      <a href="prefecture_detail.php"><img src="./img/サムネ２.jpg" width="400px"></a>
    </div>
    <div class="Thumbnail2">
      <a href="course_detail.php"><img src="./img/サムネ２.jpg" width="400px"></a>
      <a href="prefecture_detail.php"><img src="./img/サムネ２.jpg" width="400px"></a>
    </div>
  </div>
>>>>>>> admin_testcreate

 <div style="text-align: right; margin-top: 20px;">
    <a href="course_list.php">その他動画はこちらから</a>
  </div>
</div>
<!-- /コンテンツ-->
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

//ページを作成
$page_obj = new cnode();
//ヘッダ追加
$page_obj->add_child(cutil::create('cheader'));
//本体追加
$page_obj->add_child(cutil::create('cmain_node'));
//フッタ追加
$page_obj->add_child(cutil::create('cfooter'));
//構築時処理
$page_obj->create();
//ページ全体を表示
$page_obj->display();


?>


