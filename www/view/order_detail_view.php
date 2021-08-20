<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  <title>注文詳細</title>
</head>
<body>
  <?php include VIEW_PATH . 'templates/header_logined.php'; ?>
  <h1>注文詳細</h1>
  <ul>
    <li>注文番号: <?php print($order_id); ?></li>
    <li>購入日時: <?php print($created); ?></li>
    <li>合計金額: <?php print(number_format($total_price)); ?>円</li>
  </ul>
  <div class="container">

    <?php include VIEW_PATH . 'templates/messages.php'; ?>

    <?php if(count($order_details) > 0){ ?>    
      <table class="table table-bordered">
        <thead class="thead-light">
          <tr>
            <th>商品名</th>
            <th>購入価格</th>
            <th>購入数</th>
            <th>小計</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($order_details as $order_detail){ ?>
          <tr>
            <td><?php print($order_detail['name']); ?></td>
            <td><?php print($order_detail['price']); ?></td>
            <td><?php print($order_detail['quantity']); ?></td>
            <td><?php print($order_detail['price'] * $order_detail['quantity']); ?>円</td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    <?php } else { ?>
      <p>注文詳細はありません。</p>
    <?php } ?> 
  </div>
</body>
</html>