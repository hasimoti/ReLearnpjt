<?php
/*!
@file privacy.php
@brief プライバシーポリシーページ
@copyright Copyright (c) 2024 Yamanoi Yasushi.
*/
require_once("common/libs.php");

class cmain_node extends cnode {
    public function display() {
        echo <<<END_BLOCK
<!-- コンテンツ　-->
<div class="contents">
  <h5><strong>プライバシーポリシー</strong></h5>
  <p>
    本サービスは、利用者の個人情報の保護に最大限努めます。<br>
    ・取得した個人情報は、サービス提供および運営目的以外には利用しません。<br>
    ・法令に基づく場合を除き、第三者に提供することはありません。<br>
    ・個人情報の開示・訂正・削除等のご要望は、運営者までご連絡ください。
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