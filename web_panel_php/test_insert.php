<?php
$db = new PDO('mysql:host=localhost;dbname=smm_db', 'root', 'Root@123');

// 1. Follow Order
$db->query("INSERT INTO orders (uid, service_id, status, charge, quantity, remains, link, created, changed) VALUES (39, 2, 'processing', 0, 10, 10, 'https://www.instagram.com/naveen_kariyawasam98/', NOW(), NOW())");
$order_id_1 = $db->lastInsertId();

for ($i=0; $i<10; $i++) {
    $data = json_encode(['delivery_speed' => 'instant', 'sequence' => $i+1, 'batch_size' => 1]);
    $db->query("INSERT INTO automation_tasks (order_id, service_id, action, target, data, status, created, changed) VALUES ($order_id_1, 2, 'follow', 'https://www.instagram.com/naveen_kariyawasam98/', '$data', 0, NOW(), NOW())");
}

// 2. Like Order
$db->query("INSERT INTO orders (uid, service_id, status, charge, quantity, remains, link, created, changed) VALUES (39, 1, 'processing', 0, 10, 10, 'https://www.instagram.com/p/DYDuwknCp91/', NOW(), NOW())");
$order_id_2 = $db->lastInsertId();

for ($i=0; $i<10; $i++) {
    $data = json_encode(['delivery_speed' => 'instant', 'sequence' => $i+1, 'batch_size' => 1]);
    $db->query("INSERT INTO automation_tasks (order_id, service_id, action, target, data, status, created, changed) VALUES ($order_id_2, 1, 'like', 'https://www.instagram.com/p/DYDuwknCp91/', '$data', 0, NOW(), NOW())");
}

// 3. Comment Order
$db->query("INSERT INTO orders (uid, service_id, status, charge, quantity, remains, link, created, changed) VALUES (39, 3, 'processing', 0, 5, 5, 'https://www.instagram.com/p/DYDuwknCp91/', NOW(), NOW())");
$order_id_3 = $db->lastInsertId();

for ($i=0; $i<5; $i++) {
    $data = json_encode(['delivery_speed' => 'instant', 'comment_strategy' => 'ai_generated', 'sequence' => $i+1, 'batch_size' => 1]);
    $db->query("INSERT INTO automation_tasks (order_id, service_id, action, target, data, status, created, changed) VALUES ($order_id_3, 3, 'comment', 'https://www.instagram.com/p/DYDuwknCp91/', '$data', 0, NOW(), NOW())");
}
echo "Orders placed: $order_id_1, $order_id_2, $order_id_3\n";
