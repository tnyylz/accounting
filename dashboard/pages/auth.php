<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Oturum başlatılmamışsa başlatılır
}
include("../../login-signup/connect.php");

if (!isset($_SESSION["login"])) {
    $_SESSION['message'] = "Erişim için giriş yapınız!";
    header("Location: ../../login-signup/login.php");
    exit(0);
}

?>