<?php
  // function nexseed(){
  //   echo "seedくん";
  // }
  // 同じ関数名の関数を宣言するとエラーが出る
  // function nexseed($greeting){
  //   echo $greeting."、seedくん";
  // }
  
  //   function nexseed($greeting,$name){
  //   echo $greeting."、".$name;
  // }

  function checkExam($score){
    // if($score > 80){
    //   return "合格!";
    // } else {
    //   return "不合格!";
    // }


    $kekka = "";
    if ($score > 80) {
      $kekka = "";
    }else{
      $kekka = "";
    }
    return $kekka;
  }

  function nexseed($greeting,$name){
    return $greeting.'、'.$name;

  }
 ?>
 <?php
  // echo nexseed("カムバック","いまじん");
  $aisatu = nexseed("カムバック","いまじん");
  echo $aisatu;
  ?>