<?php
$host = 'localhost';
$db = 'user_login';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $captcha = $_POST['g-recaptcha-response'];

    $secretKey = "6LdKsJsqAAAAAIwiwF577UjUgvgw6nH9IPlJX-y-";
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$captcha");
    $responseKeys = json_decode($response, true);

    if (!$responseKeys['success']) {
        die("Captcha tidak valid! Silakan coba lagi.");
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            echo "Login berhasil! Selamat datang, " . htmlspecialchars($username);
        } else {
            echo "Password salah.";
        }
    } else {
        echo "Akun tidak ditemukan.";
    }

    $stmt->close();
}
$conn->close();
?>
