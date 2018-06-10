<?php 
  session_start();

  if (isset($_GET["user_id"])) {
    $user_id = $_GET["user_id"];
  } else {
    $user_id = $_SESSION["id"];
  }

  require('dbconnect.php');
  require('function.php');

  $signin_user = get_signin_user($dbh,$_SESSION["id"]);

  $sql = 'SELECT * FROM `users` WHERE `id`=?';
  $data = array($user_id);
  $stmt = $dbh->prepare($sql);
  $stmt->execute($data);

  $record = $stmt->fetch(PDO::FETCH_ASSOC);

  $following_sql = 'SELECT `fw`.*,`u`.`name`,`u`.`img_name`,`u`.`created` FROM `followers` AS `fw` LEFT JOIN `users` AS `u` ON `fw`.`follower_id`=`u`.`id` WHERE `user_id`=?';
  $following_data = array($user_id);
  $following_stmt = $dbh->prepare($following_sql);
  $following_stmt->execute($following_data);

  $following = array();

  while (true) {
    $following_record = $following_stmt->fetch(PDO::FETCH_ASSOC);
    if ($following_record == false) {
      break;
    }
    $following[] = $following_record;
  }

  $followers_sql = 'SELECT `fw`.*,`u`.`name`,`u`.`img_name`,`u`.`created` FROM `followers` AS `fw` LEFT JOIN `users` AS `u` ON `fw`.`user_id`=`u`.`id` WHERE `follower_id`=?';
  $followers_data = array($user_id);
  $followers_stmt = $dbh->prepare($followers_sql);
  $followers_stmt->execute($followers_data);

  $followers = array();

  $follow_flag = 0;
  // ログインユーザーが今見ているプロフィールページの人をフォローしていたら1、フォローしていなかったら0

  while (true) {
    $followers_record = $followers_stmt->fetch(PDO::FETCH_ASSOC);
    if ($followers_record == false) {
      break;
    }
        // フォロワーの中に、ログインしている人がいるかチェック
    if ($followers_record["user_id"] == $_SESSION["id"]) {
      $follow_flag = 1;
    }
    $followers[] = $followers_record;
    // var_dump($followers_record);

  }
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
<body style="margin-top: 60px; background: #E4E6EB;">
  <?php include("navbar.php"); ?>

  <div class="container">
    <div class="row">
      <div class="col-xs-3 text-center">
        <img src="user_profile_img/<?php echo $record['img_name']; ?>" class="img-thumbnail" />
        <h2><?php echo $record['name']; ?></h2>
        <?php if ( $user_id != $_SESSION["id"]) { ?>
          <?php if ($follow_flag == 0) { ?>
            <a href="follow.php?follower_id=<?php echo $record["id"] ?>">
              <button class="btn btn-default btn-block">フォローする</button>
            </a>
          <?php } else { ?>
            <a href="unfollow.php?follower_id=<?php echo $record["id"] ?>">
              <button class="btn btn-default btn-block">フォロー解除する</button>
            </a>
          <?php } ?>
        <?php } ?>

      </div>

      <div class="col-xs-9">
        <ul class="nav nav-tabs">
          <li class="active">
            <a href="#tab1" data-toggle="tab">Followers</a>
          </li>
          <li>
            <a href="#tab2" data-toggle="tab">Following</a>
          </li>
        </ul>
        <!--タブの中身-->
        <div class="tab-content">
          <div id="tab1" class="tab-pane fade in active">
          <?php foreach ($followers as $follower) {?>
            <div class="thumbnail">
              <div class="row">
                <div class="col-xs-2">
                  <img src="user_profile_img/<?php echo $follower['img_name']; ?>" width="80">
                </div>
                <div class="col-xs-10">
                  <?php echo $follower["name"] ?><br>
                  <a href="#" style="color: #7F7F7F;"><?php echo $follower["created"]; ?>からメンバー</a>
                </div>
              </div>
            </div>
          <?php } ?>
          </div>
          <div id="tab2" class="tab-pane fade">
          <?php foreach ($following as $followings) {?>
            <div class="thumbnail">
              <div class="row">
                <div class="col-xs-2">
                  <img src="user_profile_img/<?php echo $followings['img_name']; ?>" width="80">
                </div>
                <div class="col-xs-10">
                  <?php echo $followings["name"] ?><br>
                  <a href="#" style="color: #7F7F7F;"><?php echo $followings["created"]; ?>からメンバー</a>
                </div>
              </div>
            </div>
          <?php } ?>
          </div>
        </div>

      </div><!-- class="col-xs-12" -->

    </div><!-- class="row" -->
  </div><!-- class="cotainer" -->
  <script src="assets/js/jquery-3.1.1.js"></script>
  <script src="assets/js/jquery-migrate-1.4.1.js"></script>
  <script src="assets/js/bootstrap.js"></script>
</body>
</html>