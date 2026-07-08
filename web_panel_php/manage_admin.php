<?php
define('PHPASS_HASH_STRENGTH', 8);
define('PHPASS_HASH_PORTABLE', FALSE);
require_once "app/third_party/MX/PasswordHash.php";

$app_hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
$password = "admin1234";
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
    
    $email = 'YOUR_AIRPROXY_USER@gmail.com';
    $ids = md5(uniqid(rand(), true));
    
    // 1. Delete all other staff accounts
    $pdo->prepare("DELETE FROM general_staffs WHERE email != ?")->execute([$email]);
    echo "Other staff accounts deleted.\n";
    
    // 2. Check if user exists
    $stmt = $pdo->prepare("SELECT id FROM general_staffs WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user) {
        // Update existing admin
        $stmt = $pdo->prepare("UPDATE general_staffs SET password = ?, admin = 1, status = 1 WHERE id = ?");
        $stmt->execute([$hashed_password, $user['id']]);
        echo "Admin account upgraded successfully with ID: " . $user['id'] . "\n";
    } else {
        // Insert new admin
        $sql = "INSERT INTO general_staffs (ids, role_id, admin, first_name, last_name, email, password, timezone, status, created, changed) 
                VALUES (?, 1, 1, 'Super', 'Admin', ?, ?, 'Asia/Colombo', 1, NOW(), NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$ids, $email, $hashed_password]);
        echo "New Admin account created successfully!\n";
    }
} catch (\PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
