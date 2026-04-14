<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=food_app;charset=utf8', 'root', '');
$tables = [];
foreach($pdo->query('SHOW TABLES') as $row) {
    array_push($tables, $row[0]);
}
foreach($tables as $table) {
    if (strpos(strtolower($table), 'avail') !== false) {
        echo "Found table: $table\n";
        foreach($pdo->query("DESCRIBE $table") as $col) {
            echo "  " . $col['Field'] . " - " . $col['Type'] . "\n";
        }
    }
}
echo "Done.\n";
