<?php
include 'db.php';

// Nombre d'utilisateurs
$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$stats['users'] = $stmt->fetchColumn();

// Nombre de commandes
$stmt = $pdo->query("SELECT COUNT(*) FROM orders");
$stats['orders'] = $stmt->fetchColumn();

// Revenu total
$stmt = $pdo->query("SELECT SUM(amount) FROM orders");
$stats['revenue'] = $stmt->fetchColumn();
?>