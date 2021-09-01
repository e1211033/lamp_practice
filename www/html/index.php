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

$items = get_open_items($db);
// 並べ替えの初期設定は新着順とする
$sort_key = '0';
$popular_items = get_open_popular_items($db,$sort_key);

/* トークンの生成 */
$token = get_csrf_token();
include_once VIEW_PATH . 'index_view.php';