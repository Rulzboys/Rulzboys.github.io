<?php
// Konfigurasi database
$host = 'localhost'; // Server database
$db = 'user_login';  // Nama database
$user = 'root';      // Username database
$pass = '';          // Password database

// Membuat koneksi ke database
$conn = new mysqli($host, $user, $pass, $db);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Proses pendaftaran jika form dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $captcha = $_POST['g-recaptcha-response'];

    // Validasi CAPTCHA
    $secretKey = "6LdKsJsqAAAAAIwiwF577UjUgvgw6nH9IPlJX-y-";
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$captcha");
    $responseKeys = json_decode($response, true);

    if (!$responseKeys['success']) {
        die("Captcha tidak valid! Silakan coba lagi.");
    }

    // Validasi password dan email
    if ($password !== $confirm_password) {
        die("Password dan konfirmasi password tidak cocok!");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Email tidak valid!");
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Cek apakah username atau email sudah terdaftar
    $stmt_check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt_check->bind_param("ss", $username, $email);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        die("Username atau email sudah terdaftar!");
    }

    // Simpan data ke database
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashed_password);

    if ($stmt->execute()) {
        echo "Pendaftaran berhasil! Silakan login.";
    } else {
        echo "Terjadi kesalahan: " . $stmt->error;
    }

    $stmt->close();
    $stmt_check->close();
}

// Tutup koneksi database
$conn->close();
?>
