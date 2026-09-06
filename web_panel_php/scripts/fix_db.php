<?php
$mysqli = new mysqli('127.0.0.1', 'root', 'Root@123', 'smm_db');

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Check active accounts
$res = $mysqli->query("SELECT count(*) as count FROM ig_accounts WHERE status = 1");
$active = $res->fetch_assoc()['count'];
echo "Active accounts: $active\n";

// Check pending tasks
$res = $mysqli->query("SELECT count(*) as count FROM automation_tasks WHERE status = 0");
$pending = $res->fetch_assoc()['count'];
echo "Pending tasks: $pending\n";

// Check pending comment tasks
$res = $mysqli->query("SELECT count(*) as count FROM automation_tasks WHERE status = 0 AND action = 'comment'");
$pending_comments = $res->fetch_assoc()['count'];
echo "Pending comment tasks: $pending_comments\n";

// Delete old follow tasks to clear the queue
if (true) {
    $mysqli->query("DELETE FROM automation_tasks WHERE status = 0 AND action = 'follow'");
    echo "Cleared old follow tasks.\n";
    
    // Reactivate one account for testing
    $mysqli->query("UPDATE ig_accounts SET status = 1 LIMIT 1");
    echo "Reactivated 1 IG account.\n";
}

$mysqli->close();
?>
