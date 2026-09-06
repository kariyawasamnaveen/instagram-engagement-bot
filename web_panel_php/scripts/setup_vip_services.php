<?php
$host = '127.0.0.1';
$db   = 'smm_db';
$user = 'root';
$pass = 'Root@123';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    // 1. Create Category
    $cate_ids = md5(uniqid(rand(), true));
    $cate_name = '💎 ELITE VIP MONTHLY PACKAGES';
    $stmt = $pdo->prepare("INSERT INTO categories (ids, name, status, sort, created, changed) VALUES (?, ?, 1, 1, NOW(), NOW())");
    $stmt->execute([$cate_ids, $cate_name]);
    $cate_id = $pdo->lastInsertId();
    echo "Category created with ID: $cate_id\n";
    
    // 2. Create 3 Services
    $services = [
        ['name' => '$59 VIP Elite [INSTANT]', 'desc' => '100 Likes per post + 100 Followers (Instant Delivery)'],
        ['name' => '$59 VIP Elite [ORGANIC]', 'desc' => '100 Likes per post + 100 Followers (Natural Growth)'],
        ['name' => '$59 VIP Elite [SLOW]', 'desc' => '100 Likes per post + 100 Followers (Safety First)']
    ];
    
    foreach ($services as $index => $s) {
        $s_ids = md5(uniqid(rand(), true));
        $sql = "INSERT INTO services (ids, cate_id, name, `desc`, price, min, max, add_type, type, status, sort, created, changed) 
                VALUES (?, ?, ?, ?, 59.0000, 100, 100, 'manual', 'subscriptions', 1, ?, NOW(), NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$s_ids, $cate_id, $s['name'], $s['desc'], $index + 1]);
        echo "Service '{$s['name']}' created.\n";
    }
    
    echo "Database setup complete!\n";

} catch (\PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
