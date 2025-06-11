<?php
/*!
@file index.php
@brief メインメニュー
@copyright Copyright (c) 2024 Yamanoi Yasushi.
*/

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
<table class="table table-bordered">
<thead>

</thead>
<tbody>


</tbody><div class="Thumbnails">
	<div class="Thumbnail1">
			<a href="./user/course_list.php"><img src="./img/サムネサンプル.jpg" width="400px" >
			<a href="./user/CourseDetails.php"> <img src="./img/サムネ２.jpg"  width="400px" ></a>
	</div>
	<div class="Thumbnail2">
			<a href="./course_detail.php"> <img src="./img/サムネ２.jpg"  width="400px" ></a>
			<a href="./prefecture_detail.php"> <img src="./img/サムネ２.jpg"  width="400px" ></a>
	</div>
</div>
</table>
</div>
<!-- /コンテンツ　-->
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
