<?php
/********************************

auth_adm.php

管理者ログイン認証
認証が必要なページはこのファイルをインクルードする
すでにlibs.phpがインクルードされている必要がある
*複数のサイトが同居またはユーザー管理と混同しないため
$_SESSIONは多次元配列にする

             2024/5/20 Y.YAMANOI
*********************************/
session_start();
if((!isset($_SESSION['tmY2024_adm']['admin_login'])) 
    || (!isset($_SESSION['tmY2024_adm']['admin_master_id']))){
    cutil::redirect_exit("admin_login.php");
}
$admin = new cadmin_master();
$row = $admin->get_tgt_login(false,$_SESSION['tmY2024_adm']['admin_login']);
if($row === false || !isset($row['admin_master_id'])){
    cutil::redirect_exit("admin_login.php");
}

if($row['admin_master_id'] != $_SESSION['tmY2024_adm']['admin_master_id']){
    cutil::redirect_exit("admin_login.php");
}
if($row['admin_login'] != $_SESSION['tmY2024_adm']['admin_login']){
    cutil::redirect_exit("admin_login.php");
}

function get_admin_name(){
    if(isset($_SESSION['tmY2024_adm']['admin_name'])){
        return $_SESSION['tmY2024_adm']['admin_name'];
    }
	else{
		return '';
	}
}


?>
