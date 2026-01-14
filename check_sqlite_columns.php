<?php
$db = new PDO('sqlite:database/database.sqlite');
$stmt = $db->query("PRAGMA table_info(companies)");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($columns as $col) {
    echo $col['name'] . "\n";
}
