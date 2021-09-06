<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';

session_start();
$_SESSION = array();
$params = session_get_cookie_params();
setcookie(session_name(), '', time() - 42000,
  $params["path"], 
  $params["domain"],
  $params["secure"], 
  $params["httponly"]
);
session_destroy();

// cookieに保存されているsort情報を削除
setcookie('sort', '', time() - 42000);

/* トークンの生成 */
$token = get_csrf_token();
redirect_to(LOGIN_URL);

