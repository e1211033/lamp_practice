<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
require_once MODEL_PATH . 'order.php';

session_start();

if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

$db = get_db_connect();
$user = get_login_user($db);

$now_page = get_get('now_page');

// $now_pageが''の場合場合
if(!$now_page){
  // cookie(now_page)に$now_pageの情報が保存されていない場合、1ページ目を取得する
  if(!$_COOKIE['now_page']){
    $now_page = '1';
  // cookie(now_page)に$now_pageの情報が保存されている場合、cookie(now_page)に保存されているページ番号を取得する
} else {
    $now_page = $_COOKIE['now_page'];
  }
}
// $now_pageの内容をcookieに保存
setcookie('now_page', $now_page, time()+3600);
// 現在のページ番号に対応する商品を取得
$items = get_open_items($db, $now_page);
// 商品ページの総ページ数を取得
$total_pages = get_total_numbers_of_pages($db);
$popular_items = get_open_popular_items($db);

/* トークンの生成 */
$token = get_csrf_token();
include_once VIEW_PATH . 'index_view.php';