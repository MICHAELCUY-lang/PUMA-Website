<?php
$host = '127.0.0.1';
$rootUser = 'root';
$rootPass = '';

try {
    $pdo = new PDO("mysql:host=$host", $rootUser, $rootPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `puma_backend`");
    echo "Database puma_backend check/creation successful.\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), "could not find driver") !== false) {
         die("Error: MySQL driver not found. Please enable extension=pdo_mysql in your php.ini.\n");
    }
    die("DB Error: " . $e->getMessage());
}
?>
