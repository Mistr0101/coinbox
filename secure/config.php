<?php
define('MAINTENANCE_MODE', false); // Changez cette valeur à false pour désactiver le mode maintenance

if (MAINTENANCE_MODE) {
    header('Location: maintenance_status.php');
    exit;
}

$host = 'localhost';
$db = 'u210893834_crypto';
$user = 'u210893834_crypto';
$pass = 'OYwadpGpps34@';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database $db :" . $e->getMessage());
}
?>