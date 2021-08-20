<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'order.php';

session_start();

/* トークンの照合 */
collation_csrf_token(get_post('token'), ORDER_URL);

if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

$db = get_db_connect();
$user = get_login_user($db);

$order_id = get_post('order_id');
$created = get_post('created');
$total_price = get_post('total_price');

$order_details = get_order_detail_display($db, $order_id);

/* トークンの生成 */
$token = get_csrf_token();
include_once VIEW_PATH . 'order_detail_view.php';