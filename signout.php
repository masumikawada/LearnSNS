<?php 
    session_start();

    $_SESSION=array();

    session_destroy();

    header('Location: register/signin.php');
 ?>