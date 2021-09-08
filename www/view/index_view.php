<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  
  <title>商品一覧</title>
  <link rel="stylesheet" href="<?php print(STYLESHEET_PATH . 'index.css'); ?>">
</head>
<body>
  <?php include VIEW_PATH . 'templates/header_logined.php'; ?>
  

  <div class="container">

    <form action="index.php" method="get">
      <div class="row justify-content-end">
        <div class="col-2">
          <select name="sort" class="form-control">
<?php       foreach ($sort_list as $key => $value) {?>
<?php         if (isset($sort_key) && ((int)$sort_key === $key)) {?>
                <option value = "<?php print ($key); ?>" selected><?php print $value; ?></option>
<?php         } else {?>
                <option value = "<?php print ($key); ?>"><?php print $value; ?></option>
<?php         }?>
<?php       }?> 
          </select>
        </div>
        <div class="col-1">
          <input type="submit" value="並び替え">
        </div>
      </div>
    </form>

    <h1>商品一覧</h1>
    <?php include VIEW_PATH . 'templates/messages.php'; ?>

    <div class="card-deck">
      <div class="row">
      <?php foreach($items as $item){ ?>
        <div class="col-6 item">
          <div class="card h-100 text-center">
            <div class="card-header">
              <?php print(h($item['name'])); ?>
            </div>
            <figure class="card-body">
              <img class="card-img" src="<?php print(IMAGE_PATH . $item['image']); ?>">
              <figcaption>
                <?php print(number_format($item['price'])); ?>円
                <?php if($item['stock'] > 0){ ?>
                  <form action="index_add_cart.php" method="post">
                    <input type="submit" value="カートに追加" class="btn btn-primary btn-block">
                    <input type="hidden" name="item_id" value="<?php print($item['item_id']); ?>">
                    <input type="hidden" name="token" value="<?php print ($token);?>">
                    <input type="hidden" name="sort" value="<?php print ($sort_key); ?>">
                  </form>
                <?php } else { ?>
                  <p class="text-danger">現在売り切れです。</p>
                <?php } ?>
              </figcaption>
            </figure>
          </div>
        </div>
      <?php } ?>
      </div>
    </div>
  </div>

  <div class="container mt-5">
    <nav aria-label="Page navigation example">
      <p class="d-flex justify-content-center"><?php print($total_number_of_items)?>件中 <?php print($start_number_of_items)?> - <?php print($end_number_of_items)?>件目の商品</p>
      <ul class="pagination justify-content-center">
        <?php if($now_page === '1'){?>
          <li class="page-item disabled"><a class="page-link" href="<?php print(HOME_PAGINATION_URL.'1');?>">最初へ</a></li>
          <li class="page-item disabled"><a class="page-link" href="<?php print(HOME_PAGINATION_URL).((int)$now_page-1);?>">前へ</a></li>
        <?php } else {?>
          <li class="page-item"><a class="page-link" href="<?php print(HOME_PAGINATION_URL.'1');?>">最初へ</a></li>
          <li class="page-item"><a class="page-link" href="<?php print(HOME_PAGINATION_URL).((int)$now_page-1);?>">前へ</a></li>
        <?php }?>
        <?php for($i = 1; $i <= $total_number_of_pages; $i++){ ?>
          <?php if($i === (int)$now_page){?>
            <li class="page-item active" aria-current="page"><a class="page-link" href="<?php print(HOME_PAGINATION_URL).$i;?>"><?php print($i)?></a></li>
          <?php } else {?>
            <li class="page-item"><a class="page-link" href="<?php print(HOME_PAGINATION_URL).$i;?>"><?php print($i)?></a></li>
          <?php }?>
        <?php }?>
        <?php if($now_page === (string)$total_number_of_pages){?>
          <li class="page-item disabled"><a class="page-link" href="<?php print(HOME_PAGINATION_URL).((int)$now_page+1);?>">次へ</a></li>
          <li class="page-item disabled"><a class="page-link" href="<?php print(HOME_PAGINATION_URL.$total_number_of_pages);?>">最後へ</a></li>
        <?php } else {?>
          <li class="page-item"><a class="page-link" href="<?php print(HOME_PAGINATION_URL).((int)$now_page+1);?>">次へ</a></li>
          <li class="page-item"><a class="page-link" href="<?php print(HOME_PAGINATION_URL.$total_number_of_pages);?>">最後へ</a></li>
        <?php }?>
      </ul>
    </nav>
  </div>

  <div class="container border-top mt-5">
    <h1 class="mt-3">人気ランキング</h1>
    <div class="card-deck">
      <div class="row">
      <?php foreach($popular_items as $index => $popular_item){ ?>
        <div class="col-4 item">
          <div class="card h-100 text-center">
            <h4 class="card-header">
              <?php print ("第" . ($index+1) . "位：" . h($popular_item['name'])); ?>
            </h4>
            <figure class="card-body">
              <img class="card-img" src="<?php print(IMAGE_PATH . $popular_item['image']); ?>">
              <figcaption>
                <?php print(number_format($popular_item['price'])); ?>円
                <?php if($popular_item['stock'] > 0){ ?>
                  <form action="index_add_cart.php" method="post">
                    <input type="submit" value="カートに追加" class="btn btn-primary btn-block">
                    <input type="hidden" name="item_id" value="<?php print($popular_item['item_id']); ?>">
                    <input type="hidden" name="token" value="<?php print ($token);?>">
                    <input type="hidden" name="sort" value="<?php print ($sort_key); ?>">
                  </form>
                <?php } else { ?>
                  <p class="text-danger">現在売り切れです。</p>
                <?php } ?>
              </figcaption>
            </figure>
          </div>
        </div>
      <?php } ?>
      </div>
    </div>
  </div>

</body>
</html>