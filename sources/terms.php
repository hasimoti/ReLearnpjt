<?php
/*!
@file terms.php
@brief 利用規約ページ
@copyright Copyright (c) 2024 Yamanoi Yasushi.
*/
require_once("common/libs.php");

class cmain_node extends cnode {
    public function display() {
        echo <<<END_BLOCK
<!-- コンテンツ　-->
<div class="contents">
  <h5><strong>利用規約</strong></h5>
  <p>
    本サービスの利用にあたっては、以下の利用規約に同意したものとみなします。<br>
    ・本サービスの著作権は運営者に帰属します。<br>
    ・利用者は法令および公序良俗に反する行為を行ってはなりません。<br>
    ・サービス内容は予告なく変更・終了する場合があります。<br>
    ・その他、詳細は運営者の指示に従ってください。
  </p>
  <div style="margin-top:30px;">
    <a href="index.php">メインメニューへ戻る</a>
  </div>
</div>
<!-- /コンテンツ-->
END_BLOCK;
    }
}

$page_obj = new cnode();
$page_obj->add_child(cutil::create('cheader'));
$page_obj->add_child(cutil::create('cmain_node'));
$page_obj->add_child(cutil::create('cfooter'));
$page_obj->create();
$page_obj->display();
?>