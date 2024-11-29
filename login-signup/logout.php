<?php 
require "connect.php";
session_start();
if (isset($_POST["logout"])) {
    unset($_SESSION["login"]);
    unset($_SESSION["role"]);
    unset($_SESSION["username"]);
    unset($_SESSION["kullanici_id"]);


    $_SESSION['message'] = "Başarılı bir şekilde çıkış yapılmıştır.";
    header("Location: login.php");
    exit(0);
}
?>