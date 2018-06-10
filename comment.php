<?php 
  session_start();

  require("dbconnect.php");

  $login_user_id = $_SESSION["id"];
  $comment = $_POST["write_comment"];
  $feed_id = $_POST["feed_id"];

  $sql = "INSERT INTO `comments` SET `comment`=?, `user_id`=?, feed_id=?, `created`=NOW()";

  $data = array($comment,$login_user_id,$feed_id);
  $stmt = $dbh->prepare($sql);
  $stmt->execute($data);

  // feedsテーブルにコメントカウントをupdateする
  //SQL文を作成
  $update_sql = "UPDATE `feeds` SET `comment_count` = `comment_count`+1 WHERE `id` = ?";
  $update_data = array($feed_id);
  $update_stmt = $dbh->prepare($update_sql);
  $update_stmt->execute($update_data);

  header("Location: timeline.php");

?>