<?php
  session_start();

  $user_id = $_SESSION["id"];
  $follower_id = $_GET["follower_id"];

  require('dbconnect.php');

  $sql = "INSERT INTO `followers` SET `user_id`=?, `follower_id`=?";

  $data = array($user_id,$follower_id);
  $stmt = $dbh->prepare($sql);
  $stmt->execute($data);

  header("Location: profile.php?user_id=".$follower_id);

 ?>