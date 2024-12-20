<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $recaptchaToken = $_POST['g-recaptcha-response'];

    // Kunci rahasia reCAPTCHA
    $secretKey = '6LdKsJsqAAAAAIwiwF577UjUgvgw6nH9IPlJX-y-';

    // Verifikasi reCAPTCHA
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$recaptchaToken");
    $responseKeys = json_decode($response, true);

    if ($responseKeys['success'] && $responseKeys['score'] >= 0.5) {
        // reCAPTCHA berhasil
        // Menggunakan prepared statement untuk keamanan
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Verifikasi password
            if (password_verify($password, $user['password'])) {
                // Login berhasil, arahkan ke halaman index.html
                echo "<script>alert('Login berhasil!');</script>";
                header("Location: index.html");
                exit();
            } else {
                echo "<script>alert('Password salah!'); window.location.href = 'login.html';</script>";
            }
        } else {
            echo "<script>alert('Email tidak ditemukan!'); window.location.href = 'login.html';</script>";
        }
        $stmt->close();
        $conn->close();
    } else {
        // reCAPTCHA gagal
        echo "<script>alert('Verifikasi reCAPTCHA gagal! Silakan coba lagi.'); window.location.href = 'login.html';</script>";
    }
}
?>
