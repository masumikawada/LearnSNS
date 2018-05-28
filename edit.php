<?php 
  //feed_idを取得
  $feed_id = $_GET["feed_id"];

  //編集したいfeedsテーブルのデータを取得して、入力欄に初期表示させる
  //ポイント
  //書いた人の情報も表示したいので、テーブル結合を使う(timelineと同じもの)
  //編集したいfeeds tableのデータは一件だけです(繰り返し処理は必要ないよ)
   require('dbconnect.php');
  //SQL文作成
  $sql = 'SELECT `f`.*,`u`.`name`,`u`.`img_name` FROM `feeds` AS `f` LEFT JOIN `users` AS `u` ON `f`.`user_id`=`u`.`id` WHERE `f`.`id`=? ';
  //SQL文実行
  $data = array($feed_id);
  $stmt = $dbh->prepare($sql);
  $stmt->execute($data);
  //フェッチ
  $feed = $stmt->fetch(PDO::FETCH_ASSOC);

  // HTML内にデータ表示の処理を記述
  // var_dump($feed);

 ?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>Learn SNS</title>
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="assets/font-awesome/css/font-awesome.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
  </head>
  <body style="margin-top: 60px">
    <div calss="container">
      <div class="row">
        <!-- ここにコンテンツ -->
        <div class="col-xs-4 col-xs-offset-4">
          <form action='update.php' method="POST">
            <img src="user_profile_img/<?php echo $feed['img_name'] ?>" width="60">
            <?php echo $feed['name'] ?><br>
            <?php echo $feed['created'] ?><br>
            <textarea class="form-control" name="feed"><?php echo $feed['feed'] ?></textarea>
           <input type="hidden" name="feed_id" value="<?php echo $feed_id ?>">
            <input type="submit" value="更新" class="btn btn-warning btn-xs">
          </form>
        </div>
      </div>

    </div>
    <script src="assets/js/jquery-3.1.1.js"></script>
    <script src="assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="assets/js/bootstrap.js"></script>
  </body>
</html>