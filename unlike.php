<?php
  //get送信
  $feed_id = $_GET["feed_id"];
  //var_dump($_GET);
  // １．データベースに接続する
  require('dbconnect.php');

  // ２．SQL文を実行する
  $sql = "DELETE FROM `likes` WHERE `likes`.`feed_id`=?";
  // SQLインジェクション対策
  $data = array($feed_id);
  $stmt = $dbh->prepare($sql);
  $stmt->execute($data);

  // 一覧view.php に戻る
   header("Location: timeline.php");

?>