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
$items = get_open_items($db, $sort_key);
$popular_items = get_open_popular_items($db);

/* トークンの生成 */
$token = get_csrf_token();
include_once VIEW_PATH . 'index_view.php';