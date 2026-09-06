<?php
define('PHPASS_HASH_STRENGTH', 8);
define('PHPASS_HASH_PORTABLE', FALSE);
require_once "app/third_party/MX/PasswordHash.php";

$app_hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
$password = "user1234";
$hashed_password = $app_hasher->HashPassword($password);

$host = '127.0.0.1';
$db   = 'smm_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    $email = 'user@gmail.com';
    $ids = md5(uniqid(rand(), true));
    $first_name = "Regular";
    $last_name = "User";
    $balance = 1000.0000;
    
    // Check if user exists
    $stmt = $pdo->prepare("SELECT id FROM general_users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user) {
        // Update existing user
        $stmt = $pdo->prepare("UPDATE general_users SET password = ?, balance = ?, status = 1 WHERE id = ?");
        $stmt->execute([$hashed_password, $balance, $user['id']]);
        echo "User updated successfully with ID: " . $user['id'];
    } else {
        // Insert new user
        $sql = "INSERT INTO general_users (ids, first_name, last_name, email, password, balance, status, timezone, login_type, created, changed) 
                VALUES (?, ?, ?, ?, ?, ?, 1, 'Asia/Colombo', 'Sign_up_page', NOW(), NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$ids, $first_name, $last_name, $email, $hashed_password, $balance]);
        echo "User created successfully!";
    }
} catch (\PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
