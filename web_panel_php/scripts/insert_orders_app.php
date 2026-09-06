<?php
$db = new PDO('mysql:host=localhost;dbname=smm_db', 'root', 'Root@123');

// 1. Follow Order (service 2, action 'follow')
$db->query("INSERT INTO orders (uid, service_id, status, charge, quantity, remains, link, created, changed) VALUES (39, 2, 'processing', 0, 5, 5, 'helpa.global', NOW(), NOW())");
$order_id_1 = $db->lastInsertId();

for ($i=0; $i<5; $i++) {
    $data = json_encode(['delivery_speed' => 'instant', 'sequence' => $i+1, 'batch_size' => 1]);
    $db->query("INSERT INTO automation_tasks (order_id, service_id, action, target, data, status, created, changed) VALUES ($order_id_1, 2, 'follow', 'helpa.global', '$data', 0, NOW(), NOW())");
}

// 2. Like Order (service 1, action 'like')
$db->query("INSERT INTO orders (uid, service_id, status, charge, quantity, remains, link, created, changed) VALUES (39, 1, 'processing', 0, 5, 5, 'https://www.instagram.com/p/DYrdcX8lBSC/', NOW(), NOW())");
$order_id_2 = $db->lastInsertId();

for ($i=0; $i<5; $i++) {
    $data = json_encode(['delivery_speed' => 'instant', 'sequence' => $i+1, 'batch_size' => 1]);
    $db->query("INSERT INTO automation_tasks (order_id, service_id, action, target, data, status, created, changed) VALUES ($order_id_2, 1, 'like', 'https://www.instagram.com/p/DYrdcX8lBSC/', '$data', 0, NOW(), NOW())");
}

// 3. Comment Order (service 3, action 'comment')
$db->query("INSERT INTO orders (uid, service_id, status, charge, quantity, remains, link, created, changed) VALUES (39, 3, 'processing', 0, 5, 5, 'https://www.instagram.com/p/DYrdcX8lBSC/', NOW(), NOW())");
$order_id_3 = $db->lastInsertId();

for ($i=0; $i<5; $i++) {
    $data = json_encode(['delivery_speed' => 'instant', 'comment_strategy' => 'ai_generated', 'sequence' => $i+1, 'batch_size' => 1]);
    $db->query("INSERT INTO automation_tasks (order_id, service_id, action, target, data, status, created, changed) VALUES ($order_id_3, 3, 'comment', 'https://www.instagram.com/p/DYrdcX8lBSC/', '$data', 0, NOW(), NOW())");
}

// 4. Share Order (service 4, action 'share')
$db->query("INSERT INTO orders (uid, service_id, status, charge, quantity, remains, link, created, changed) VALUES (39, 4, 'processing', 0, 5, 5, 'https://www.instagram.com/p/DYrdcX8lBSC/', NOW(), NOW())");
$order_id_4 = $db->lastInsertId();

for ($i=0; $i<5; $i++) {
    $data = json_encode(['delivery_speed' => 'instant', 'sequence' => $i+1, 'batch_size' => 1]);
    $db->query("INSERT INTO automation_tasks (order_id, service_id, action, target, data, status, created, changed) VALUES ($order_id_4, 4, 'share', 'https://www.instagram.com/p/DYrdcX8lBSC/', '$data', 0, NOW(), NOW())");
}

echo "Orders placed: Follows($order_id_1), Likes($order_id_2), Comments($order_id_3), Shares($order_id_4)\n";
?>
