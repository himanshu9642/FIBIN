<?php
// FIBIN - Food Donation Platform database configuration
$host = 'localhost';
$db   = 'fibin_food';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die('<div style="font-family:Arial;padding:40px;max-width:800px;margin:auto"><h2>FIBIN database connection failed</h2><p>Please create/import the <b>database.sql</b> file in phpMyAdmin and check <b>config/database.php</b>.</p><pre style="background:#111;color:#fff;padding:15px;border-radius:10px">'.htmlspecialchars($e->getMessage()).'</pre></div>');
}
