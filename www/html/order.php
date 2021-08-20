<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'order.php';

session_start();

if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

$db = get_db_connect();
$user = get_login_user($db);

$orders = get_order_history($db, $user);
$order_details = get_order_detail($db, $user);

$total_price = array();
foreach($order_details as $order_detail){
  $total_price[$order_detail['order_id']] += $order_detail['price'] * $order_detail['quantity'];
}

/* トークンの生成 */
$token = get_csrf_token();
include_once VIEW_PATH . 'order_view.php';