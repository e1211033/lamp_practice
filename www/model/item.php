<?php
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';

// DB利用

function get_item($db, $item_id){
  /* 値を直接代入からPDOStatement::executeのバインド機能を使用したのもに修正 */
  $sql = "
    SELECT
      item_id,
      name,
      stock,
      price,
      image,
      status
    FROM
      items
    WHERE
      item_id = :item_id
  ";
  /* $item_idをPDOStatement::execute用の配列に格納 */
  $params = array(':item_id' => $item_id);
  return fetch_query($db, $sql, $params);
}

function get_items($db, $is_open = false, $sort_key = '0', $page = false){
  /* 値を直接代入からPDOStatement::executeのバインド機能を使用したのもに修正 */
  $sql = '
    SELECT
      item_id,
      name,
      stock,
      price,
      image,
      status
    FROM
      items
  ';
  if($is_open === true){
    $sql .= '
      WHERE status = 1
    ';
  }

  $sql .= 'ORDER BY ';
  if($sort_key === '0') {
    $sql .= 'created DESC';    
  } elseif($sort_key === '1') {
    $sql .= 'price ASC';    
  } elseif($sort_key === '2') {
    $sql .= 'price DESC';    
  }

  if($page){
    $sql .= '
      LIMIT :page, :pagination
    ';
    /* $item_idをPDOStatement::execute用の配列に格納 */
    $params = array(':page' => PAGINATION_NUMBER*($page-1), 'pagination' => PAGINATION_NUMBER);
    return fetch_all_query($db, $sql,  $params);
  }

  return fetch_all_query($db, $sql);
}

function get_all_items($db){
  return get_items($db);
}

function get_open_items($db, $sort_key = '0', $page = '1'){
  return get_items($db, true, $sort_key, $page);
}

function regist_item($db, $name, $price, $stock, $status, $image){
  $filename = get_upload_filename($image);
  if(validate_item($name, $price, $stock, $filename, $status) === false){
    return false;
  }
  return regist_item_transaction($db, $name, $price, $stock, $status, $image, $filename);
}

function regist_item_transaction($db, $name, $price, $stock, $status, $image, $filename){
  $db->beginTransaction();
  if(insert_item($db, $name, $price, $stock, $filename, $status)
    && save_image($image, $filename)){
    $db->commit();
    return true;
  }
  $db->rollback();
  return false;

}

function insert_item($db, $name, $price, $stock, $filename, $status){
  $status_value = PERMITTED_ITEM_STATUSES[$status];
  /* 値を直接代入からPDOStatement::executeのバインド機能を使用したのもに修正 */
  $sql = "
    INSERT INTO
      items(
        name,
        price,
        stock,
        image,
        status
      )
    VALUES(:name, :price, :stock, :filename, :status_value);
  ";
  /* $name, $price, $stock, $filename, $status_valueをPDOStatement::execute用の配列に格納 */
  $params = array(':name' => $name, ':price' => $price, ':stock' => $stock, ':filename' => $filename, ':status_value' => $status_value);
  return execute_query($db, $sql, $params);
}

function update_item_status($db, $item_id, $status){
  /* 値を直接代入からPDOStatement::executeのバインド機能を使用したのもに修正 */
  $sql = "
    UPDATE
      items
    SET
      status = :status
    WHERE
      item_id = :item_id
    LIMIT 1
  ";
  /* $statusおよび$item_idをPDOStatement::execute用の配列に格納 */
  $params = array(':status' => $status, ':item_id' => $item_id);
  return execute_query($db, $sql, $params);
}

function update_item_stock($db, $item_id, $stock){
  /* 値を直接代入からPDOStatement::executeのバインド機能を使用したのもに修正 */
  $sql = "
    UPDATE
      items
    SET
      stock = :stock
    WHERE
      item_id = :item_id
    LIMIT 1
  ";
  /* $stockおよび$item_idをPDOStatement::execute用の配列に格納 */
  $params = array(':stock' => $stock, ':item_id' => $item_id);
  return execute_query($db, $sql, $params);
}

function destroy_item($db, $item_id){
  $item = get_item($db, $item_id);
  if($item === false){
    return false;
  }
  $db->beginTransaction();
  if(delete_item($db, $item['item_id'])
    && delete_image($item['image'])){
    $db->commit();
    return true;
  }
  $db->rollback();
  return false;
}

function delete_item($db, $item_id){
  /* 値を直接代入からPDOStatement::executeのバインド機能を使用したのもに修正 */
  $sql = "
    DELETE FROM
      items
    WHERE
      item_id = :item_id
    LIMIT 1
  ";
  /* $item_idをPDOStatement::execute用の配列に格納 */
  $params = array(':item_id' => $item_id);
  return execute_query($db, $sql, $params);
}


// 非DB

function is_open($item){
  return $item['status'] === 1;
}

function validate_item($name, $price, $stock, $filename, $status){
  $is_valid_item_name = is_valid_item_name($name);
  $is_valid_item_price = is_valid_item_price($price);
  $is_valid_item_stock = is_valid_item_stock($stock);
  $is_valid_item_filename = is_valid_item_filename($filename);
  $is_valid_item_status = is_valid_item_status($status);

  return $is_valid_item_name
    && $is_valid_item_price
    && $is_valid_item_stock
    && $is_valid_item_filename
    && $is_valid_item_status;
}

function is_valid_item_name($name){
  $is_valid = true;
  if(is_valid_length($name, ITEM_NAME_LENGTH_MIN, ITEM_NAME_LENGTH_MAX) === false){
    set_error('商品名は'. ITEM_NAME_LENGTH_MIN . '文字以上、' . ITEM_NAME_LENGTH_MAX . '文字以内にしてください。');
    $is_valid = false;
  }
  return $is_valid;
}

function is_valid_item_price($price){
  $is_valid = true;
  if(is_positive_integer($price) === false){
    set_error('価格は0以上の整数で入力してください。');
    $is_valid = false;
  }
  return $is_valid;
}

function is_valid_item_stock($stock){
  $is_valid = true;
  if(is_positive_integer($stock) === false){
    set_error('在庫数は0以上の整数で入力してください。');
    $is_valid = false;
  }
  return $is_valid;
}

function is_valid_item_filename($filename){
  $is_valid = true;
  if($filename === ''){
    $is_valid = false;
  }
  return $is_valid;
}

function is_valid_item_status($status){
  $is_valid = true;
  if(isset(PERMITTED_ITEM_STATUSES[$status]) === false){
    $is_valid = false;
  }
  return $is_valid;
}


/**
 * 商品ページの総ページ数を取得
 * 
 * @param   int $total_items  商品の総数
 * @return  int $total_pages  商品ページの総ページ数
 */

function get_total_number_of_pages($total_items){
  $total_pages = floor($total_items/PAGINATION_NUMBER);
  if(($total_items%PAGINATION_NUMBER) !== 0){
    $total_pages += 1;
  }
  return $total_pages;
}


/**
 * 取得する商品の先頭の件数
 * 
 * @param   int $now_page                           表示する商品一覧ページのページ番号
 * @return  (PAGINATION_NUMBER*($now_page-1) + 1)   表示する商品一覧ページの先頭の商品の件数
 */

function get_start_number_of_items($now_page){
  return (PAGINATION_NUMBER*($now_page-1) + 1);
}


/**
 * 取得する商品の末尾の件数
 * 
 * @param   int $total_items    取得した商品の総数
 * @param   int $start_items    表示する商品一覧ページの先頭の商品の件数
 * @return  int $end_items      表示する商品一覧ページの末尾の商品の件数
 */

function get_end_number_of_items($total_items, $start_items){
  $tmp = $total_items-$start_items;
  if($tmp < (PAGINATION_NUMBER-1)){
    $end_items = $start_items + $tmp;
  } else {
    $end_items = $start_items + (PAGINATION_NUMBER-1);
  }
  return $end_items;
}


/**
 * 公開設定のアイテムの総数を取得
 * 
 * @param   obj $db                       DBハンドル
 * @param   str $is_open                  商品ステータス
 * @return  (get_total_numbers_of_items)  公開設定のアイテムの総数
 */

function get_total_number_of_open_items($db){
  $tmp = get_number_of_items($db, true);
  return $tmp['count'];
}


/**
 * 取得したアイテムの件数を取得(取得が8件中何件か判定する)
 * 
 * @param   obj $db             DBハンドル
 * @param   str $is_open        商品ステータス
 * @param   int $page           ページ番号
 * @return  (fetch_all_query)   クエリの応答結果
 */

function get_number_of_items($db, $is_open = false, $page=false){
  $sql = '
    SELECT
      COUNT(item_id) AS count
    FROM
      items
  ';
  if($is_open === true){
    $sql .= '
      WHERE status = 1
    ';
  }
  if($page){
    $sql .= '
      LIMIT :page, 8
    ';
    /* $item_idをPDOStatement::execute用の配列に格納 */
    $params = array(':page' => PAGINATION_NUMBER*($page-1));
    return fetch_query($db, $sql, $params);
  }
  return fetch_query($db, $sql);
} 