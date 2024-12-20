<?php
$servername = "192.168.0.100"; // Ganti dengan nama host dari FreeHosting
$username = "users";  // Ganti dengan username database kamu
$password = "Syahrur@28";  // Ganti dengan password database kamu
$dbname = "fruitotc_user_login";        // Ganti dengan nama database kamu

// Membuat Koneksi
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Cek Koneksi
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
