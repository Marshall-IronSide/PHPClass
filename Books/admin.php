<?php
include 'config.php';

// Simple admin authentication (in production, use proper authentication)
$admin_logged_in = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'];

if (isset($_POST['admin_login'])) {
    // Simple login check (in production, use proper password hashing)
    if ($_POST['admin_username'] === 'admin' && $_POST['admin_password'] === 'password123') {
        $_SESSION['admin_logged_in'] = true;
        $admin_logged_in = true;
    } else {
        $login_error = "Invalid credentials";
    }
}

if (isset($_POST['logout'])) {
    unset($_SESSION['admin_logged_in']);
    $admin_logged_in = false;
}

// Handle book operations
if ($admin_logged_in) {
    // Add new book
    if (isset($_POST['add_book'])) {
        $title = trim($_POST['title']);
        $author = trim($_POST['author']);
        $isbn = trim($_POST['isbn']);
        $price = floatval($_POST['price']);
        $stock_quantity = intval($_POST['stock_quantity']);
        $category_id = intval($_POST['category_id']);
        $description = trim($_POST['description']);
        $image_url = trim($_POST['image_url']);
        $featured = isset($_POST['featured']) ? 1 : 0;
        
        if ($title && $author && $price > 0) {
            $stmt = $pdo->prepare("INSERT INTO books (title, author, isbn, price, stock_quantity, category_id, description, image_url, featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$title, $author, $isbn, $price, $stock_quantity, $category_id, $description, $image_url, $featured])) {
                $success_message = "Book added successfully!";
            } else {
                $error_message = "Error adding book.";
            }
        } else {
            $error_message = "Please fill in all required fields.";
        }
    }
    
    // Update book
    if (isset($_POST['update_book'])) {
        $book_id = intval($_POST['book_id']);
        $title = trim($_POST['title']);
        $author = trim($_POST['author']);
        $isbn = trim($_POST['isbn']);
        $price = floatval($_POST['price']);
        $stock_quantity = intval($_POST['stock_quantity']);
        $category_id = intval($_POST['category_id']);
        $description = trim($_POST['description']);
        $image_url = trim($_POST['image_url']);
        $featured = isset($_POST['featured']) ? 1 : 0;
        
        if ($title && $author && $price > 0) {
            $stmt = $pdo->prepare("UPDATE books SET title = ?, author = ?, isbn = ?, price = ?, stock_quantity = ?, category_id = ?, description = ?, image_url = ?, featured = ? WHERE id = ?");
            if ($stmt->execute([$title, $author, $isbn, $price, $stock_quantity, $category_id, $description, $image_url, $featured, $book_id])) {
                $success_message = "Book updated successfully!";
            } else {
                $error_message = "Error updating book.";
            }
        } else {
            $error_message = "Please fill in all required fields.";
        }
    }
    
    // Delete book
    if (isset($_POST['delete_book'])) {
        $book_id = intval($_POST['book_id']);
        $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
        if ($stmt->execute([$book_id])) {
            $success_message = "Book deleted successfully!";
        } else {
            $error_message = "Error deleting book.";
        }
    }
    
    // Add category
    if (isset($_POST['add_category'])) {
        $category_name = trim($_POST['category_name']);
        if ($category_name) {
            $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
            if ($stmt->execute([$category_name])) {
                $success_message = "Category added successfully!";
            } else {
                $error_message = "Error adding category.";
            }
        }
    }
    
    // Delete category
    if (isset($_POST['delete_category'])) {
        $category_id = intval($_POST['category_id']);
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        if ($stmt->execute([$category_id])) {
            $success_message = "Category deleted successfully!";
        } else {
            $error_message = "Error deleting category.";
        }
    }
    
    // Get all books
    $books = $pdo->query("SELECT b.*, c.name as category_name FROM books b LEFT JOIN categories c ON b.category_id = c.id ORDER BY b.created_at DESC")->fetchAll();
    
    // Get all categories
    $categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
    
    // Get recent orders
    $recent_orders = $pdo->query("SELECT o.*, COUNT(oi.id) as item_count FROM orders o LEFT JOIN order_items oi ON o.id = oi.order_id GROUP BY o.id ORDER BY o.created_at DESC LIMIT 10")->fetchAll();
    
    // Get statistics
    $stats = [];
    $stats['total_books'] = $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn();
    $stats['total_orders'] = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    $stats['total_revenue'] = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE status = 'completed'")->fetchColumn() ?: 0;
    $stats['low_stock'] = $pdo->query("SELECT COUNT(*) FROM books WHERE stock_quantity < 10")->fetchColumn();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookStore | Compte</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            max-width: 1200px;
            width: 100%;
        }
        
        .logo {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            color: white;
        }
        
        .logo i {
            font-size: 2.5rem;
            margin-right: 15px;
        }
        
        .logo h1 {
            font-size: 2.8rem;
            font-weight: bold;
            text-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .card-container {
            display: flex;
            justify-content: center;
            width: 100%;
            gap: 30px;
            flex-wrap: wrap;
        }
        
        .card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.25);
            width: 100%;
            max-width: 450px;
            overflow: hidden;
            transition: transform 0.4s, box-shadow 0.4s;
        }
        
        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
        }
        
        .card-header {
            background: #2c3e50;
            color: white;
            padding: 25px;
            text-align: center;
        }
        
        .card-header h2 {
            font-size: 1.8rem;
            margin-bottom: 5px;
        }
        
        .card-body {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        .input-icon {
            position: relative;
        }
        
        .input-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #667eea;
        }
        
        .input-icon input {
            width: 100%;
            padding: 14px 14px 14px 45px;
            border: 2px solid #e1e5ee;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .input-icon input:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
        }
        
        .btn {
            display: block;
            width: 100%;
            padding: 14px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
            margin-top: 10px;
        }
        
        .btn:hover {
            background: #5a6fd8;
            transform: translateY(-2px);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .btn-secondary {
            background: #6c757d;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .btn-tertiary {
            background: transparent;
            color: #667eea;
            border: 2px solid #667eea;
        }
        
        .btn-tertiary:hover {
            background: rgba(102, 126, 234, 0.1);
        }
        
        .links {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        
        .links a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }
        
        .links a:hover {
            color: #5a6fd8;
            text-decoration: underline;
        }
        
        .footer {
            margin-top: 40px;
            color: white;
            text-align: center;
            font-size: 1rem;
        }
        
        .footer a {
            color: white;
            text-decoration: none;
            font-weight: 600;
        }
        
        .footer a:hover {
            text-decoration: underline;
        }
        
        .create-account-info {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 20px;
            max-width: 450px;
            color: #333;
        }
        
        .create-account-info h3 {
            font-size: 1.8rem;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        
        .benefits {
            list-style: none;
            margin: 25px 0;
        }
        
        .benefits li {
            margin-bottom: 15px;
            padding-left: 30px;
            position: relative;
        }
        
        .benefits li i {
            position: absolute;
            left: 0;
            top: 5px;
            color: #667eea;
            font-size: 1.2rem;
        }
        
        .password-strength {
            height: 5px;
            background: #e1e5ee;
            border-radius: 3px;
            margin-top: 8px;
            overflow: hidden;
        }
        
        .strength-meter {
            height: 100%;
            width: 0;
            background: #dc3545;
            transition: width 0.3s, background 0.3s;
        }
        
        @media (max-width: 768px) {
            .card-container {
                flex-direction: column;
                align-items: center;
            }
            
            .create-account-info {
                margin-top: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <i class="fas fa-book-open"></i>
            <h1>BookStore</h1>
        </div>
        
        <div class="card-container">
            <!-- Login Card -->
            <div class="card">
                <div class="card-header">
                    <h2>Connexion à votre compte</h2>
                    <p>Ravi de vous revoir sur BookStore !</p>
                </div>
                <div class="card-body">
                    <form id="loginForm">
                        <div class="form-group">
                            <label for="loginEmail">Adresse e-mail</label>
                            <div class="input-icon">
                                <i class="fas fa-envelope"></i>
                                <input type="email" id="loginEmail" placeholder="Entrez votre e-mail" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="loginPassword">Mot de passe</label>
                            <div class="input-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password" id="loginPassword" placeholder="Entrez votre mot de passe" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="input-icon">
                                <button type="submit" class="btn">Se connecter</button>
                            </div>
                        </div>
                        
                        <div class="links">
                            <a href="#" id="forgotPassword">Mot de passe oublié ?</a>
                            <a href="#" id="createAccountLink">Créer un compte</a>
                        </div>
                    </form>
                    
                    <div class="form-group" style="margin-top: 30px;">
                        <a href="index.php" class="btn btn-tertiary">
                            <i class="fas fa-home"></i> Retour à l'accueil
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Create Account Card (Hidden by default) -->
            <div class="card" id="createAccountCard" style="display: none;">
                <div class="card-header">
                    <h2>Créer un nouveau compte</h2>
                    <p>Rejoignez notre communauté de lecteurs !</p>
                </div>
                <div class="card-body">
                    <form id="createAccountForm">
                        <div class="form-group">
                            <label for="fullName">Nom complet</label>
                            <div class="input-icon">
                                <i class="fas fa-user"></i>
                                <input type="text" id="fullName" placeholder="Entrez votre nom complet" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Adresse e-mail</label>
                            <div class="input-icon">
                                <i class="fas fa-envelope"></i>
                                <input type="email" id="email" placeholder="Entrez votre e-mail" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Mot de passe</label>
                            <div class="input-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password" id="password" placeholder="Créez un mot de passe" required>
                            </div>
                            <div class="password-strength">
                                <div class="strength-meter" id="passwordStrength"></div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirmPassword">Confirmez le mot de passe</label>
                            <div class="input-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password" id="confirmPassword" placeholder="Confirmez votre mot de passe" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="input-icon">
                                <button type="submit" class="btn">Créer le compte</button>
                            </div>
                        </div>
                    </form>
                    
                    <div class="links">
                        <a href="#" id="backToLogin">Retour à la connexion</a>
                    </div>
                    
                    <div class="form-group" style="margin-top: 20px;">
                        <a href="index.php" class="btn btn-tertiary">
                            <i class="fas fa-home"></i> Retour à l'accueil
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Account Benefits (Visible on login screen) -->
            <div class="create-account-info" id="benefitsInfo">
                <h3>Pourquoi créer un compte ?</h3>
                <ul class="benefits">
                    <li><i class="fas fa-shopping-cart"></i> Paiement plus rapide et suivi des commandes</li>
                    <li><i class="fas fa-bookmark"></i> Sauvegardez vos livres et auteurs préférés</li>
                    <li><i class="fas fa-tags"></i> Profitez de réductions et offres exclusives</li>
                    <li><i class="fas fa-bell"></i> Soyez informé des nouvelles parutions</li>
                    <li><i class="fas fa-star"></i> Gagnez des récompenses avec notre programme de fidélité</li>
                </ul>
                <p>Rejoignez notre communauté de plus de 500 000 lecteurs qui profitent de recommandations personnalisées et d'avantages exclusifs aux membres.</p>
            </div>
        </div>
        
        <div class="footer">
            <p>&copy; 2023 BookStore. Tous droits réservés. | 
                <a href="#">Politique de confidentialité</a> | 
                <a href="#">Conditions d'utilisation</a>
            </p>
        </div>
    </div>
    
    <script>
        // DOM Elements
        const loginCard = document.querySelector('.card');
        const createAccountCard = document.getElementById('createAccountCard');
        const benefitsInfo = document.getElementById('benefitsInfo');
        const createAccountLink = document.getElementById('createAccountLink');
        const backToLogin = document.getElementById('backToLogin');
        const passwordInput = document.getElementById('password');
        const passwordStrength = document.getElementById('passwordStrength');
        
        // Toggle between login and create account views
        createAccountLink.addEventListener('click', function(e) {
            e.preventDefault();
            loginCard.style.display = 'none';
            createAccountCard.style.display = 'block';
            benefitsInfo.style.display = 'none';
        });
        
        backToLogin.addEventListener('click', function(e) {
            e.preventDefault();
            loginCard.style.display = 'block';
            createAccountCard.style.display = 'none';
            benefitsInfo.style.display = 'block';
        });
        
        // Password strength indicator
        passwordInput.addEventListener('input', function() {
            const password = passwordInput.value;
            let strength = 0;
            
            if (password.length >= 8) strength += 25;
            if (/[A-Z]/.test(password)) strength += 25;
            if (/[0-9]/.test(password)) strength += 25;
            if (/[^A-Za-z0-9]/.test(password)) strength += 25;
            
            passwordStrength.style.width = strength + '%';
            
            // Update color based on strength
            if (strength < 50) {
                passwordStrength.style.background = '#dc3545'; // Rouge
            } else if (strength < 75) {
                passwordStrength.style.background = '#ffc107'; // Jaune
            } else {
                passwordStrength.style.background = '#28a745'; // Vert
            }
        });
        
        // Form validation and submission
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;
            
            // Simple validation
            if (email && password) {
                alert('Connexion réussie ! Redirection vers votre compte...');
                // Dans une vraie application, la connexion serait traitée côté serveur
            }
        });
        
        document.getElementById('createAccountForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const fullName = document.getElementById('fullName').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            // Validation
            if (password !== confirmPassword) {
                alert('Les mots de passe ne correspondent pas !');
                return;
            }
            
            if (password.length < 8) {
                alert('Le mot de passe doit contenir au moins 8 caractères !');
                return;
            }
            
            // Successful account creation
            alert(`Compte créé avec succès !\nBienvenue sur BookStore, ${fullName} !`);
            // Dans une vraie application, ces données seraient envoyées au serveur
        });
    </script>
</body>
</html>