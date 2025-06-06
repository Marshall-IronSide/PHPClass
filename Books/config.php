<?php
// Database configuration
$host = 'localhost';
$db = 'books_db';
$user = 'root';
$pass = '';
$port = '3377';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Start session for cart functionality
session_start();

// Generate session ID if not exists
if (!isset($_SESSION['session_id'])) {
    $_SESSION['session_id'] = session_create_id();
}

// Helper functions
function formatPrice($price) {
    return '$' . number_format($price, 2);
}

function getCartCount() {
    global $pdo;
    $stmt = $pdo->prepare("SELECT SUM(quantity) FROM cart WHERE session_id = ?");
    $stmt->execute([$_SESSION['session_id']]);
    return $stmt->fetchColumn() ?: 0;
}

function addToCart($book_id, $quantity = 1) {
    global $pdo;
    
    // Vérifier d'abord si le livre existe et a du stock
    $stmt = $pdo->prepare("SELECT stock_quantity, title FROM books WHERE id = ?");
    $stmt->execute([$book_id]);
    $book = $stmt->fetch();
    
    if (!$book) {
        throw new Exception("Livre introuvable");
    }
    
    // Check if item already in cart
    $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE session_id = ? AND book_id = ?");
    $stmt->execute([$_SESSION['session_id'], $book_id]);
    $existing = $stmt->fetch();
    
    $new_quantity = $quantity;
    if ($existing) {
        $new_quantity = $existing['quantity'] + $quantity;
    }
    
    // Vérifier le stock disponible
    if ($new_quantity > $book['stock_quantity']) {
        throw new Exception("Stock insuffisant. Il ne reste que " . $book['stock_quantity'] . " exemplaire(s) de '" . $book['title'] . "'");
    }
    
    if ($existing) {
        // Update quantity
        $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + ? WHERE id = ?");
        $stmt->execute([$quantity, $existing['id']]);
    } else {
        // Insert new item
        $stmt = $pdo->prepare("INSERT INTO cart (session_id, book_id, quantity) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['session_id'], $book_id, $quantity]);
    }
}

function convertToFCFA($price_eur) {
    $taux = 655;
    return $price_eur * $taux;
}

function formatPriceFCFA($price_eur) {
    $fcfa = convertToFCFA($price_eur);
    return number_format($fcfa, 0, ',', ' ') . ' FCFA';
}
?>