<?php
//サインインしているユーザーの情報を取得して、返す関数
//引数$db:データベース接続オブジェクト
// 引数$user_id:サインインしているユーザーのid
//使い方はget_signin_user($dbh,$_SESSION["id"]);
function get_signin_user($db, $user_id){
    require('dbconnect.php');
    $sql = 'SELECT * FROM `users` WHERE `id`=?';
    $data = array($user_id);
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);
    $signin_user = $stmt->fetch(PDO::FETCH_ASSOC);

    return $signin_user;
}

function check_signin($user_id){
    if (!isset($user_id)) {
        header("Location: register/signin.php");
        exit();
    }
}

