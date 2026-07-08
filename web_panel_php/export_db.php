<?php
$host = '127.0.0.1:8889';
$user = 'root';
$pass = 'root';
$name = 'smm_db';

$mysqli = new mysqli('127.0.0.1', 'root', 'root', 'smm_db', 8889);
if ($mysqli->connect_error) {
    die("Connect Error: " . $mysqli->connect_error);
}

$tables = [];
$result = $mysqli->query("SHOW TABLES");
while ($row = $result->fetch_row()) {
    $tables[] = $row[0];
}

$dump = "-- PHP Database Export\n\n";

foreach ($tables as $table) {
    $row_create = $mysqli->query("SHOW CREATE TABLE `$table`")->fetch_row();
    $dump .= "\n\n" . $row_create[1] . ";\n\n";
    
    $result_data = $mysqli->query("SELECT * FROM `$table`");
    while ($row = $result_data->fetch_assoc()) {
        $keys = array_keys($row);
        $values = array_map(function($v) use ($mysqli) {
            if ($v === null) return 'NULL';
            return "'" . $mysqli->real_escape_string($v) . "'";
        }, array_values($row));
        
        $dump .= "INSERT INTO `$table` (`" . implode("`, `", $keys) . "`) VALUES (" . implode(", ", $values) . ");\n";
    }
}

file_put_contents('/tmp/smm_db_export.sql', $dump);
echo "Export successful: /tmp/smm_db_export.sql (" . strlen($dump) . " bytes)\n";
?>
