<?php
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
    $recaptchaToken = $_POST['g-recaptcha-response'];

    // Validasi reCAPTCHA
    $secretKey = '6LdKsJsqAAAAAIwiwF577UjUgvgw6nH9IPlJX-y-'; // Ganti dengan kunci rahasia reCAPTCHA Anda
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$recaptchaToken");
    $responseKeys = json_decode($response, true);

    if (!$responseKeys['success'] || $responseKeys['score'] < 0.5) {
        echo "<script>alert('Verifikasi reCAPTCHA gagal! Silakan coba lagi.'); window.location='register.html';</script>";
        exit;
    }

    // Validasi Password
    if ($password !== $confirm_password) {
        echo "<script>alert('Password dan Konfirmasi Password tidak cocok!'); window.location='register.html';</script>";
        exit;
    }

    // Hash Password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Simpan ke Database
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashed_password);

    if ($stmt->execute()) {
        echo "<script>alert('Selamat! Anda telah berhasil membuat akun. Silahkan login.'); window.location='login.html';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan: " . $stmt->error . "'); window.location='register.html';</script>";
    }

    $stmt->close();
}

mysqli_close($conn);
?>
