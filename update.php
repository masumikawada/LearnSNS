<?php 
    $feed_id = $_POST["feed_id"];
    $updated_feed = $_POST['feed'];
  //編集したいfeedsテーブルのデータを取得して、入力欄に初期表示させる
  //ポイント
  //書いた人の情報も表示したいので、テーブル結合を使う(timelineと同じもの)
  //編集したいfeeds tableのデータは一件だけです(繰り返し処理は必要ないよ)
   require('dbconnect.php');
  //SQL文作成
  $sql = 'UPDATE `feeds` SET `feed`=? WHERE `id`=? ';
  //SQL文実行
  $data = array($updated_feed,$feed_id);
  $stmt = $dbh->prepare($sql);
  $stmt->execute($data);

  header('Location: timeline.php');

 ?>