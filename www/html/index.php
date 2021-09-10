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

// 並び替えの種類判定用変数にGETで投稿されたsortの情報を$sort_keyに代入
$sort_key = get_get('sort');
// ページ番号判定用変数($now_page)にGETで投稿されたのnow_pageの情報を代入
$now_page = get_get('now_page');

// $sort_keyが$sort_listのいずれのキーにも該当しない場合
if (!array_key_exists($sort_key,$sort_list)) {
  // cookie(sort)に$sort_keyの情報が保存されていてかつcookie(sort)が$sort_listのいずれかのキーに該当する場合、cookieの情報を$sort_keyに代入
  if (isset($_COOKIE['sort']) && array_key_exists($_COOKIE['sort'],$sort_list)) {
    $sort_key = $_COOKIE['sort'];
  // cookie(sort)に$sort_keyの情報が保存されていないまたはcookie(sort)が$sort_listのいずれのキーにも該当しない場合、商品一覧の表示は新着順($sort_key=0)とする
  } else {
    $sort_key = '0';
  }
}
// $sort_keyの内容をcookieに保存
setcookie('sort', $sort_key, time()+3600);

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

// 現在のページ番号および並べ替えに対応する商品を取得
$items = get_open_items($db, $sort_key, $now_page);
// 商品ページの総商品取得数を取得
$total_number_of_items = get_total_number_of_open_items($db);
// 商品ページの総ページ数を取得
$total_number_of_pages = get_total_number_of_pages($total_number_of_items);
// 商品ページで最初に取得するアイテムの件数
$start_number_of_items = get_start_number_of_items($now_page);
// 商品ページで最後に取得するアイテムの件数
$end_number_of_items = get_end_number_of_items($total_number_of_items, $start_number_of_items);
$popular_items = get_open_popular_items($db);

/* トークンの生成 */
$token = get_csrf_token();
include_once VIEW_PATH . 'index_view.php';