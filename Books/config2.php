<?php
// Configuration de la base de données
$host = 'localhost';
$port = '3377'; // Port personnalisé
$dbname = 'admin_db';
$username = 'root'; // Remplacez par votre nom d'utilisateur MySQL
$password = '';     // Remplacez par votre mot de passe MySQL

try {
    $pdo2 = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>