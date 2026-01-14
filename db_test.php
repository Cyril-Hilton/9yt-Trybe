<?php
try {
    $dsn = 'mysql:host=localhost;port=3306;dbname=rbfiqhyo_dev';
    $user = 'rbfiqhyo_dev';
    $password = 'Ghrte432ghd';
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    
    $pdo = new PDO($dsn, $user, $password, $options);
    echo "Connection successful!";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    // Also try root/empty which is common for XAMPP just in case
    try {
        $pdo = new PDO('mysql:host=localhost;port=3306', 'root', '');
        echo "\nFailed with provided creds, but connected as root (local default).";
    } catch (PDOException $e2) {
         echo "\nAlso failed as root.";
    }
}
