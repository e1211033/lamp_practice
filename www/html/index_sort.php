<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
require_once MODEL_PATH . 'order.php';

session_start();

/* トークンの照合 */
collation_csrf_token(get_post('token'), HOME_URL);

if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

$db = get_db_connect();
$user = get_login_user($db);

// 並び替えの種類判定用変数にPOSTで投稿されたsort_keyを
$sort_key = get_post('sort');

// $sort_keyが$sort_listのいずれのキーにも該当しない場合、商品一覧の表示は新着順($sort_key=0)とする
if (!array_key_exists($sort_key,$sort_list)) {
  $sort_key = '0';
}
$items = get_open_items($db, $sort_key);
$popular_items = get_open_popular_items($db);

/* トークンの生成 */
$token = get_csrf_token();
include_once VIEW_PATH . 'index_view.php';