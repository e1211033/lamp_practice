<?php
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';


/**
 * 注文履歴更新処理
 * 
 * @param   obj $db               DBハンドル
 * @param   int $user_id          購入したユーザのid
 * @return  (execute_query())     sql文の実行結果(true or false)
 */

function order_history_update($db, $carts, $user_id){
  if(insert_order_history(
      $db,
      $user_id
    ) === false) {
    set_error('注文履歴の更新に失敗しました。');
    return false;
  }
  $order_id = get_last_insert_id($db);
  foreach($carts as $cart){
    if(insert_order_detail_history(
        $db,
        $order_id['order_id'],
        $cart['item_id'],
        $cart['amount'],
        $cart['price']
      ) === false){
      set_error($cart['name'] . 'の注文詳細の更新に失敗しました。');
      return false;
    }
  }
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
 * 最近挿入されたデータのid取得
 * 
 * @param   obj $db             DBハンドル
 * @return  (fetch_query())     最後に挿入されたデータのid または false
 */

function get_last_insert_id($db){
  $sql = "
    SELECT order_id
    FROM   `order`
    WHERE  created = (SELECT MAX(created) FROM `order`);
  ";
  return fetch_query($db, $sql);
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