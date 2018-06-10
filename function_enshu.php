<?php
  // 練習問題1
  // function multiplication($math1,$math2) {
  //   return $math1*$math2;
  // }

  // $result = multiplication(4,6);
  // echo $result;

  // 練習問題2
  // function average($value1,$value2){
  //   return ($value1+$value2)/2;
  // }

  // $result = average(120,80);
  // echo $result;

  // 練習問題3
  // function shopping($value1,$value2){
  //   return $value1-$value2;
  // }

  // $result = shopping(1000,198);
  // echo $result;

  // 練習問題4
  function compare($value1,$value2){
    if ($value1 >= $value2) {
      $result = $value1;
    } else {
      $result = $value2;
    }
    return $result;
  }


  $game = compare(80,28);
  echo $game;