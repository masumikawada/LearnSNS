<?php
    session_start();
    // var_dump($_SESSION);
    // echo "とんだよ";
?>

<?php
  //require(dbconnect)
  // select usersテーブルから　$sessionに保存されているidを使って一件だけ取り出す
  //$signin_user に取り出したレコードを代入する
  //写真と名前をレコードから取り出す
  //$img_nameに写真のファイル名を入れる
  //$nameに名前を代入する
    require('dbconnect.php');
    $sql = 'SELECT * FROM `users` WHERE `id`=?';
            $data = array($_SESSION['id']);
            $stmt = $dbh->prepare($sql);
            $stmt->execute($data);
            $signin_user = $stmt->fetch(PDO::FETCH_ASSOC);
            // var_dump($signin_user);
            // $name=$signin_user['name'];
            // $img_name=$signin_user['img_name'];
            // var_dump($signin_user);

    $errors=array();
    // ボタン押したとき
    if (!empty($_POST)) {
         $feed = $_POST['feed'];

        if ($feed != '') {
            // 2.sql文の実行
            $sql = 'INSERT INTO `feeds` SET `feed`=?, `user_id`=?, `created`=NOW()';
            $data = array($feed, $signin_user['id']);
            $stmt = $dbh->prepare($sql);
            $stmt->execute($data);

            header('Location: timeline.php');
            exit();

        }else{
          $errors['feed'] = 'blank';
        }
    }
    //検索ボタンが押されたら、あいまい検索
    //検索ボタンが押された＝GET送信されたsearch_wordというキーのデータがある
    if(isset($_GET['search_word']) == true){
      //あいまい検索用SQL(LIKE演算子)
      $sql = 'SELECT `f`.*,`u`.`name`,`u`.`img_name` FROM `feeds` AS `f` LEFT JOIN `users` AS `u` ON `f`.`user_id`=`u`.`id` WHERE `f`.`feed` LIKE "%'.$_GET['search_word'].'%" ORDER BY `id` DESC';



    }else{
    //通常(検索ボタンが押されていない時)
    // LEFT JOINで全件取得
      $sql = 'SELECT `f`.*,`u`.`name`,`u`.`img_name` FROM `feeds` AS `f` LEFT JOIN `users` AS `u` ON `f`.`user_id`=`u`.`id` WHERE 1 ORDER BY `id` DESC';
  }
    $data = array();
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);
    // var_dump($stmt);
    // var_dump($record);
    $feeds=array();
  // 表示用の配列を初期化
    while (true) {
        $record = $stmt->fetch(PDO::FETCH_ASSOC);//ここより上でfetchしてない？
        if ($record == false) {
            break;
        }
        // like数を取得するSQL文を作成
        $like_sql = "SELECT COUNT(*) AS `like_cnt` FROM `likes` WHERE `feed_id` = ?";

        $like_data = array($record["id"]);
        // SQL文を実行
        $like_stmt = $dbh->prepare($like_sql);
        $like_stmt->execute($like_data);
        // like数を取得
        $like = $like_stmt->fetch(PDO::FETCH_ASSOC);
        // $like = array("like_cnt")
        $record["like_cnt"] = $like["like_cnt"];

        $like_flag_sql = 'SELECT COUNT(*)as `like_flag` FROM `likes` WHERE `user_id`=? AND `feed_id`=?';

        $like_flag_data = array($_SESSION["id"],$record["id"]);
        $like_flag_stmt = $dbh->prepare($like_flag_sql);
        $like_flag_stmt->execute($like_flag_data);

        $like_flag = $like_flag_stmt->fetch(PDO::FETCH_ASSOC);
        var_dump($like_flag);
        if ($like_flag["like_flag"] > 0) {
          $record["like_flag"] = 1;
        } else {
          $record["like_flag"] = 0;
        }

        $feeds[] = $record;//配列への要素追加
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
    <div class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse1" aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Learn SNS</a>
        </div>
        <div class="collapse navbar-collapse" id="navbar-collapse1">
          <ul class="nav navbar-nav">
            <li class="active"><a href="#">タイムライン</a></li>
            <li><a href="#">ユーザー一覧</a></li>
          </ul>
          <form method="GET" action="" class="navbar-form navbar-left" role="search">
            <div class="form-group">
              <input type="text" name="search_word" class="form-control" placeholder="投稿を検索">
            </div>
            <button type="submit" class="btn btn-default">検索</button>
          </form>
          <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><img src="user_profile_img/<?php echo $signin_user['img_name']; ?>" width="18" class="img-circle"><?php echo $signin_user['name']; ?><span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="#">マイページ</a></li>
                <li><a href="signout.php">サインアウト</a></li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <div class="container">
      <div class="row">
        <div class="col-xs-3">
          <ul class="nav nav-pills nav-stacked">
            <li class="active"><a href="timeline.php?feed_select=news">新着順</a></li>
            <li><a href="timeline.php?feed_select=likes">いいね！済み</a></li>
            <!-- <li><a href="timeline.php?feed_select=follows">フォロー</a></li> -->
          </ul>
        </div>
        <div class="col-xs-9">
          <div class="feed_form thumbnail">
            <form method="POST" action="timeline.php">
              <div class="form-group">
                <textarea name="feed" class="form-control" rows="3" placeholder="Happy Hacking!" style="font-size: 24px;"></textarea><br>
              </div>
              <input type="submit" value="投稿する" class="btn btn-primary">
              <?php if(isset($errors['feed']) && $errors['feed'] == 'blank') { ?>
              <p class="alert-danger">投稿データを入力してください
                  </p>
              <?php } ?>
            </form>
          </div>
          <?php foreach($feeds as $feed){ ?>
          <!-- $feed=$feeds[] []内は繰り上がる-->
            <div class="thumbnail">
              <div class="row">
                <div class="col-xs-1">
                  <img src="user_profile_img/<?php echo $feed['img_name']; ?>" width="40">
                </div>
                <div class="col-xs-11">
                  <?php echo $feed['name']; ?><br>
                  <a href="#" style="color: #7F7F7F;"><?php echo $feed['created']; ?></a>
                </div>
              </div>
              <div class="row feed_content">
                <div class="col-xs-12" >
                  <span style="font-size: 24px;"><?php echo $feed['feed'] ?></span>
                </div>
              </div>
              <div class="row feed_sub">
                <div class="col-xs-12">
                  <!-- いいねボタンを表示 -->
                  <?php if($feed["like_flag"] == 0){ ?>
                  <a href="like.php?feed_id=<?php echo $feed["id"]; ?>">
                      <button class="btn btn-default btn-xs"><i class="fa fa-thumbs-up" aria-hidden="true"></i>いいね！</button>
                  </a>
                  <?php }else{ ?>
                  <!-- いいねを取り消すボタンを表示 -->
                  <a href="unlike.php?feed_id=<?php echo $feed["id"]; ?>">
                      <button class="btn btn-default btn-xs"><i class="fa fa-thumbs-down" aria-hidden="true"></i>いいね！を取り消すボタン</button>
                  </a>
                  <?php } ?>
                  <?php if ($feed["like_cnt"] > 0){ ?>
                    <span class="like_count">いいね数 : <?php echo $feed["like_cnt"]; ?></span>
                  <?php } ?>
                  <span class="comment_count">コメント数 : 9</span>

                  <?php if ($feed["user_id"] == $_SESSION["id"]){ ?>

                    <a href="edit.php?feed_id=<?php echo $feed["id"] ?>" class="btn btn-success btn-xs">編集</a>
                    <a href="delete.php?feed_id=<?php echo $feed["id"] ?>" class="btn btn-danger btn-xs"　onclick="return confirm('ホントに消すの？');">削除</a>

                  <?php } ?>
                </div>
              </div>
            </div>
            <?php } ?>
          <div aria-label="Page navigation">
            <ul class="pager">
              <li class="previous disabled"><a href="#"><span aria-hidden="true">&larr;</span> Older</a></li>
              <li class="next"><a href="#">Newer <span aria-hidden="true">&rarr;</span></a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <script src="assets/js/jquery-3.1.1.js"></script>
    <script src="assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="assets/js/bootstrap.js"></script>
  <!--   <a href="http://www.facebook.com/share.php?u={URL}" rel="nofollow" target="_blank">リンクテキスト</a> -->

<!--     <div id="fb-root"></div>
      <script>(function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = 'https://connect.facebook.net/ja_JP/sdk.js#xfbml=1&version=v3.0';
        fjs.parentNode.insertBefore(js, fjs);
      }(document, 'script', 'facebook-jssdk'));</script>

      <div class="fb-share-button" data-href="https://www.youtube.com/watch?v=UtF6Jej8yb4" data-layout="button" data-size="large" data-mobile-iframe="true"><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fdevelopers.facebook.com%2Fdocs%2Fplugins%2F&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore">シェア</a></div> -->

  </body>
</html>

