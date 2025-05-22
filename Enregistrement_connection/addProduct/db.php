<?php
$host = 'localhost';
$dbname = 'dashboard_db';
$username = 'root';
$password = '';
$port = 3377;

// Connexion à la base de données
try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>