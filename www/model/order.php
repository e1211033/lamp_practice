<?php
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';


/**
 * 注文履歴更新処理
 * 
 * @param   obj $db               DBハンドル
 * @param   int $user_id          購入したユーザのid
 * @return  true or false         注文履歴更新の可否
 */

function order_history_update($db, $carts, $user_id){
  if(insert_order_history(
      $db,
      $user_id
    ) === false) {
    set_error('注文履歴の更新に失敗しました。');
    return false;
  }
  $order_id = $db->lastInsertId('order_id');
  foreach($carts as $cart){
    if(insert_order_detail_history(
        $db,
        $order_id,
        $cart['item_id'],
        $cart['amount'],
        $cart['price']
      ) === false){
      set_error($cart['name'] . 'の注文詳細の更新に失敗しました。');
      return false;
    }
  }
  return true;
}


/**
 * 注文履歴をデータベースに保存
 * 
 * @param   obj $db               DBハンドル
 * @param   int $user_id          購入したユーザのid
 * @return  (execute_query())     sql文の実行結果(true or false)
 */

function insert_order_history($db, $user_id){
  /* 値を直接代入からPDOStatement::executeのバインド機能を使用したのもに修正 */
  $sql = "
    INSERT INTO
      `order`(user_id)
    VALUES(:user_id);
  ";
  /* $user_idをPDOStatement::execute用の配列に格納 */
  $params = array(':user_id' => $user_id);
  return execute_query($db, $sql, $params);
}


/**
 * 注文詳細保存
 * 
 * @param   obj $db               DBハンドル
 * @param   int $order_id         注文番号id
 * @param   int $item_id          商品id
 * @param   int $quantity         注文した商品の総量
 * @param   int $price            注文時の商品の価格
 * @return  (execute_query())     sql文の実行結果(true or false)
 */

function insert_order_detail_history($db, $order_id, $item_id, $quantity, $price){
  /* 値を直接代入からPDOStatement::executeのバインド機能を使用したのもに修正 */
  $sql = "
    INSERT INTO
      order_detail(
        order_id,
        item_id,
        quantity,
        price
      )
    VALUES(:order_id, :item_id, :quantity, :price);
  ";
  /* $order_id, $item_id, $quantity, $priceをPDOStatement::execute用の配列に格納 */
  $params = array(':order_id' => $order_id, ':item_id' => $item_id, ':quantity' => $quantity, ':price' => $price);
  return execute_query($db, $sql, $params);
}


/**
 * 注文履歴取得
 * 
 * @param   obj $db               DBハンドル
 * @param   str $user             ユーザー情報
 * @return  (fetch_all_query())   sql文の実行結果(データベースの内容 or false)
 */

function get_order_history($db, $user){
  /* 値を直接代入からPDOStatement::executeのバインド機能を使用したのもに修正 */
  $sql = "
    SELECT
      order_id,
      created
    FROM
      `order`
  ";
  if(is_admin($user) === false){
    $sql .= "
      WHERE 
        user_id = :user_id
    ";
    /* $user_idをPDOStatement::execute用の配列に格納 */
    $params = array(':user_id' => $user['user_id']);
  }
  $sql .= "
    ORDER BY
      created DESC
  "; 
  return fetch_all_query($db, $sql, $params);
}


/**
 * 注文詳細取得(注文履歴での合計金額計算用)
 * 
 * @param   obj $db               DBハンドル
 * @param   int $user             ユーザー情報
 * @return  (fetch_all_query())   sql文の実行結果(データベースの内容 or false)
 */

function get_order_detail($db, $user){
  /* 値を直接代入からPDOStatement::executeのバインド機能を使用したのもに修正 */
  $sql = "
    SELECT
      `order`.order_id,
      order_detail.quantity,
      order_detail.price
    FROM
      order_detail
    JOIN
      `order`
    ON
      `order`.order_id = order_detail.order_id
  ";
if(is_admin($user) === false){
  $sql .= "
    WHERE user_id = :user_id
  ";
  /* $user_idをPDOStatement::execute用の配列に格納 */
  $params = array(':user_id' => $user['user_id']);
}
  return fetch_all_query($db, $sql, $params);
}


/**
 * 注文詳細取得(注文詳細画面表示用)
 * 
 * @param   obj $db               DBハンドル
 * @param   int $order_id         注文番号id
 * @return  (fetch_all_query())   sql文の実行結果(データベースの内容 or false)
 */

function get_order_detail_display($db, $order_id){
  /* 値を直接代入からPDOStatement::executeのバインド機能を使用したのもに修正 */
  $sql = "
    SELECT
      order_detail.quantity,
      order_detail.price,
      items.name
    FROM
      order_detail
    JOIN
      items
    ON
      order_detail.item_id = items.item_id
    WHERE 
      order_id = :order_id
  ";
  /* $user_idをPDOStatement::execute用の配列に格納 */
  $params = array(':order_id' => $order_id);
  return fetch_all_query($db, $sql, $params);
}