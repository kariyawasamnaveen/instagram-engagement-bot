<?php
define('ENVIRONMENT', 'development');
require 'app/config.php';

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$result = $mysqli->query("SELECT count(*) as total FROM orders");
if ($result) {
    $row = $result->fetch_assoc();
    echo "Total orders in 'orders' table: " . $row['total'] . "\n";
} else {
    echo "Error query orders: " . $mysqli->error . "\n";
}

$result = $mysqli->query("SELECT count(*) as total FROM general_orders");
if ($result) {
    $row = $result->fetch_assoc();
    echo "Total orders in 'general_orders' table: " . $row['total'] . "\n";
}
$mysqli->close();
