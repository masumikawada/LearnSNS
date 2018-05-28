<?php
    session_start();
    $errors = array();

    if (!empty($_POST)) {  //post送信があったときに実行する
        $name = $_POST['input_name'];
        $email = $_POST['input_email'];
        $password = $_POST['input_password'];
        $count=strlen($password);

        // ユーザー名の空チェック
        if ($name == '') {
            $errors['name'] = 'blank';
        }
        else{
            require('../dbconnect.php');

            $sql='SELECT COUNT(*) as `cnt` FROM `users` WHERE `name`=?';
            $data = array($name);
            $stmt = $dbh->prepare($sql);
            $stmt->execute($data);

            $dbh = null;
            $rec=$stmt->fetch(PDO::FETCH_ASSOC);
            var_dump($rec);

            if ($rec['cnt']>0) {
            $errors['name']='duplication';
            }
        }

        if ($email == '') {
            $errors['email'] = 'blank';
        }
        else{
            require('../dbconnect.php');

            $sql='SELECT COUNT(*) as `cnt` FROM `users` WHERE `email`=?';
            $data = array($email);
            $stmt = $dbh->prepare($sql);
            $stmt->execute($data);

            $dbh = null;
            $rec=$stmt->fetch(PDO::FETCH_ASSOC);
            var_dump($rec);

            if ($rec['cnt']>0) {
            $errors['email']='duplication';
            }
        }

        if ($password == '') {
            $errors['password'] = 'blank';
        }
        elseif($count < 4 || 16 < $count){
          $errors['password']='length';
        }

        //画像名を取得
        $file_name=$_FILES['input_img_name']['name'];
        if(!empty($file_name)){
            //拡張子チェック
            $file_type=substr($file_name,-4);
            //画像名の後ろから3文字取得
            $file_type=strtolower($file_type);
            //比較するために取得した拡張子を小文字に変換
            if($file_type!='.jpg'&&$file_type!='.png'&&$file_type!='.gif'&&$file_type!='jpeg'){
                  $errors['img_name']='type';
            }
        }else{
          $errors['img_name']='blank';
        }

        // echo $file_name.'<br>';
        // echo "<pre>";
        // var_dump($_FILES);
        // echo "<pre>";
        if(empty($errors)){
            //エラーがなかった時の処理
            date_default_timezone_set('Asia/Manila');
            $date_str=date('YmdHis');
            $submit_file_name=$date_str.$file_name;
            echo $date_str;
            echo "<br>";
            echo $submit_file_name;

            move_uploaded_file($_FILES['input_img_name']['tmp_name'], '../user_profile_img/' . $submit_file_name);
            $_SESSION['register']['name'] = $_POST['input_name'];
            $_SESSION['register']['email'] = $_POST['input_email'];
            $_SESSION['register']['password'] = $_POST['input_password'];
            // 上記3つは$_SESSION['register'] = $_POST;という書き方で1文にまとめることもできます
            $_SESSION['register']['img_name'] = $submit_file_name;
            $_SESSION['ariyo']['rino'] = 'ari';
            // var_dump ($_FILES);
            header("Location: check.php");
            exit();
        }

    }

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>Learn SNS</title>
  <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="../assets/font-awesome/css/font-awesome.css">
  <!-- 追加 -->
</head>
<body style="margin-top: 60px">
  <div class="container">
    <div class="row">
      <!-- ここにコンテンツ -->
      <!-- ここから -->
      <div class="col-xs-8 col-xs-offset-2 thumbnail">
        <h2 class="text-center content_header">アカウント作成</h2>
        <form method="POST" action="signup.php" enctype="multipart/form-data">
          <div class="form-group">
            <label for="name">ユーザー名</label>
            <input type="text" name="input_name" class="form-control" id="name" placeholder="山田 太郎">
            <?php if(isset($errors['name']) && $errors['name'] == 'blank') { ?>
              <p class="text-danger">ユーザー名を入力してください</p>
            <?php } ?>
            <?php if(isset($errors['name']) && $errors['name'] == 'duplication') { ?>
              <p class="text-danger">この名前はすでに登録されています</p>
            <?php } ?>
          </div>
          <div class="form-group">
            <label for="email">メールアドレス</label>
            <input type="email" name="input_email" class="form-control" id="email" placeholder="example@gmail.com">
            <?php if(isset($errors['email']) && $errors['email'] == 'blank') { ?>
              <p class="text-danger">メールアドレスを入力してください</p>
            <?php } ?>
            <?php if(isset($errors['email']) && $errors['email'] == 'duplication') { ?>
              <p class="text-danger">このメールアドレスはすでに登録されています</p>
            <?php } ?>
          </div>
          <div class="form-group">
            <label for="password">パスワード</label>
            <input type="password" name="input_password" class="form-control" id="password" placeholder="4 ~ 16文字のパスワード">
            <?php if(isset($errors['password']) && $errors['password'] == 'blank') { ?>
              <p class="text-danger">パスワードを入力してください</p>
            <?php } ?>
            <?php if(isset($errors['password']) && $errors['password'] == 'length') { ?>
              <p class="text-danger">パスワードは4～16字で入力してください</p>
            <?php } ?>
          </div>
          <div class="form-group">
            <label for="img_name">プロフィール画像</label>
            <input type="file" name="input_img_name" id="img_name" accept="image/*">
            <?php if(isset($errors['img_name']) && $errors['img_name'] == 'type') { ?>
              <p class="text-danger">拡張子が「jpg」「png」「gif」「jpeg」の画像を選択してください</p>
            <?php } ?>

            <?php if(isset($errors['img_name']) && $errors['img_name'] == 'blank') { ?>
              <p class="text-danger">画像を選択してください</p>
            <?php } ?>
          </div>
          <input type="submit" class="btn btn-default" value="確認">
          <a href="../signin.php" style="float: right; padding-top: 6px;" class="text-success">サインイン</a>
        </form>
      </div>
      <!-- ここまで -->

    </div>
  </div>
  <script src="../assets/js/jquery-3.1.1.js"></script>
  <script src="../assets/js/jquery-migrate-1.4.1.js"></script>
  <script src="../assets/js/bootstrap.js"></script>
</body>
</html>