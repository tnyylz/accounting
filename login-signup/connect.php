<?php 
$conn = mysqli_connect("localhost", "root","","muhasebe2");
$conn->set_charset("utf8");

if (!$conn) {
    die("Bağlantı Kurulamadı: ". mysqli_connect_error());
}
?>