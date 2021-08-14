CREATE TABLE `order` (
  order_id          int(11)     NOT NULL AUTO_INCREMENT,
  user_id           int(11)     NOT NULL,
  created           datetime    DEFAULT CURRENT_TIMESTAMP,
  primary key(order_id)
);

CREATE TABLE order_detail (
  order_detail_id   int(11)     NOT NULL AUTO_INCREMENT,
  order_id          int(11)     NOT NULL,
  item_id           int(11)     NOT NULL,
  quantity          int(11)     NOT NULL,
  price             int(11)     NOT NULL,
  primary key(order_detail_id)
);