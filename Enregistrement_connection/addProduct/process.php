<?php
include 'db.php';

// AJOUTER
if (isset($_POST['ajouter'])) {
    $product = $_POST['product'];
    $amount = floatval($_POST['amount']);
    $created_at = $_POST['created_at']; // Assurez-vous que c'est bien une date

    $stmt = $pdo->prepare("INSERT INTO orders (product, amount, created_at) VALUES (?, ?, ?)");
    $stmt->execute([$product, $amount, $created_at]);

    header('Location: ajout.php');
    exit();
}

// MODIFIER
if (isset($_POST['modifier'])) {
    $id = intval($_POST['id']);
    $product = $_POST['product'];
    $amount = floatval($_POST['amount']);

    $stmt = $pdo->prepare("UPDATE orders SET product = ?, amount = ? WHERE id = ?");
    $stmt->execute([$product, $amount, $id]);

    header('Location: ajout.php');
    exit();
}

// SUPPRIMER
if (isset($_GET['supprimer'])) {
    $id = intval($_GET['supprimer']);

    $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->execute([$id]);

    header('Location: ajout.php');
    exit();
}