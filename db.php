<?php
$conn = mysqli_connect("localhost", "root", "", "deneme1.2");

if (!$conn) {
    die("Veritabanı bağlantısı başarısız: " . mysqli_connect_error());
}
?>