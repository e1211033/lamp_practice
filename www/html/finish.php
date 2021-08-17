<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
require_once MODEL_PATH . 'cart.php';
require_once MODEL_PATH . 'order.php';

session_start();

/* トークンの照合 */
collation_csrf_token(get_post('token'), CART_URL);

if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

$db = get_db_connect();
$user = get_login_user($db);

$carts = get_user_carts($db, $user['user_id']);

$db->beginTransaction();
if(purchase_carts($db, $carts) === false
  || order_history_update($db, $carts, $user['user_id']) === false){
  $db->rollback();
  set_error('商品が購入できませんでした。');
  redirect_to(CART_URL);
}
$db->commit();

$total_price = sum_carts($carts);

/* トークンの生成 */
$token = get_csrf_token();
include_once '../view/finish_view.php';