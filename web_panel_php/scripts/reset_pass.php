<?php
// TEMPORARY - DELETE AFTER USE
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'smm_db');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    // Try with root password
    $conn = mysqli_connect('127.0.0.1', 'root', 'root', 'smm_db');
}

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$new_password = password_hash('admin123', PASSWORD_BCRYPT);
$email = 'adminYOUR_AIRPROXY_USER@gmail.com';

$sql = "UPDATE general_users SET password = '$new_password' WHERE email = '$email'";
if (mysqli_query($conn, $sql)) {
    echo "✅ Password reset successful!<br>";
    echo "Email: $email<br>";
    echo "New Password: <strong>admin123</strong><br>";
    echo "<br><strong>DELETE THIS FILE IMMEDIATELY AFTER USE!</strong>";
} else {
    echo "❌ Error: " . mysqli_error($conn);
}
mysqli_close($conn);
?>
