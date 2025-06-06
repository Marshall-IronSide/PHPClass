<?php
// Database configuration
$host = 'localhost';
$db = 'books2_db';
$user = 'root';
$pass = '';
$port = '3377';

// Establish database connection
try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Initialize session
session_start();
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookNook - Online Bookstore</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;500;400&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --accent: #e74c3c;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --success: #27ae60;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        header {
            background-color: var(--primary);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-family: 'Merriweather', serif;
            font-size: 2rem;
            font-weight: 700;
            color: white;
            text-decoration: none;
        }
        
        .logo span {
            color: var(--secondary);
        }
        
        nav ul {
            display: flex;
            list-style: none;
        }
        
        nav ul li {
            margin-left: 1.5rem;
        }
        
        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            padding: 0.5rem;
            border-radius: 4px;
            transition: background 0.3s;
        }
        
        nav ul li a:hover {
            background: rgba(255,255,255,0.1);
        }
        
        .cart-icon {
            position: relative;
            font-size: 1.2rem;
        }
        
        .cart-count {
            position: absolute;
            top: -8px;
            right: -12px;
            background: var(--accent);
            color: white;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 0.8rem;
        }
        
        .hero {
            background: linear-gradient(rgba(44, 62, 80, 0.8), rgba(44, 62, 80, 0.8)), 
                        url('https://images.unsplash.com/photo-1495446815901-a7297e633e8d?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 4rem 0;
            text-align: center;
        }
        
        .hero h1 {
            font-family: 'Merriweather', serif;
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .hero p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto 2rem;
        }
        
        .btn {
            display: inline-block;
            background: var(--secondary);
            color: white;
            padding: 0.8rem 1.5rem;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.3s;
            border: none;
            cursor: pointer;
        }
        
        .btn:hover {
            background: #2980b9;
        }
        
        .btn-accent {
            background: var(--accent);
        }
        
        .btn-accent:hover {
            background: #c0392b;
        }
        
        .section {
            padding: 3rem 0;
        }
        
        .section-title {
            font-family: 'Merriweather', serif;
            text-align: center;
            margin-bottom: 2rem;
            color: var(--dark);
            position: relative;
        }
        
        .section-title::after {
            content: '';
            display: block;
            width: 80px;
            height: 3px;
            background: var(--secondary);
            margin: 0.5rem auto;
        }
        
        .books-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 2rem;
        }
        
        .book-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .book-card:hover {
            transform: translateY(-10px);
        }
        
        .book-cover {
            height: 300px;
            background-color: #eee;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .book-cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .book-details {
            padding: 1.5rem;
        }
        
        .book-title {
            font-family: 'Merriweather', serif;
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            color: var(--dark);
        }
        
        .book-author {
            color: #666;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        
        .book-price {
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--accent);
            margin-bottom: 1rem;
        }
        
        .book-actions {
            display: flex;
            justify-content: space-between;
        }
        
        .category-nav {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        
        .category-btn {
            background: white;
            border: 2px solid var(--secondary);
            color: var(--secondary);
            padding: 0.5rem 1.5rem;
            margin: 0.5rem;
            border-radius: 30px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .category-btn:hover, .category-btn.active {
            background: var(--secondary);
            color: white;
        }
        
        .book-detail {
            display: flex;
            gap: 3rem;
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .book-detail-image {
            flex: 1;
            max-width: 400px;
            background: #eee;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .book-detail-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .book-detail-info {
            flex: 1;
        }
        
        .book-detail-title {
            font-family: 'Merriweather', serif;
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: var(--dark);
        }
        
        .book-detail-author {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 1.5rem;
        }
        
        .book-detail-price {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--accent);
            margin-bottom: 1.5rem;
        }
        
        .book-detail-description {
            margin-bottom: 2rem;
            line-height: 1.8;
        }
        
        .cart-container {
            background: white;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .cart-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
        }
        
        .cart-table th {
            text-align: left;
            padding: 1rem;
            border-bottom: 2px solid #eee;
            font-weight: 600;
        }
        
        .cart-table td {
            padding: 1rem;
            border-bottom: 1px solid #eee;
        }
        
        .cart-item-image {
            width: 80px;
            height: 100px;
            background: #eee;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .cart-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .quantity-control {
            display: flex;
            align-items: center;
        }
        
        .quantity-btn {
            width: 30px;
            height: 30px;
            background: #eee;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .quantity-input {
            width: 50px;
            height: 30px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin: 0 0.5rem;
        }
        
        .remove-btn {
            color: var(--accent);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.2rem;
        }
        
        .cart-summary {
            background: #f9f9f9;
            padding: 1.5rem;
            border-radius: 8px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
        }
        
        .summary-total {
            font-size: 1.2rem;
            font-weight: 700;
            border-top: 2px solid #eee;
            padding-top: 1rem;
            margin-top: 1rem;
        }
        
        .admin-panel {
            background: white;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .admin-form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .form-control {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: inherit;
        }
        
        .form-full {
            grid-column: 1 / -1;
        }
        
        footer {
            background: var(--dark);
            color: white;
            padding: 2rem 0;
            margin-top: 3rem;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }
        
        .footer-section h3 {
            font-family: 'Merriweather', serif;
            margin-bottom: 1rem;
            position: relative;
            padding-bottom: 0.5rem;
        }
        
        .footer-section h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 2px;
            background: var(--secondary);
        }
        
        .copyright {
            text-align: center;
            padding-top: 2rem;
            margin-top: 2rem;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        
        .page {
            min-height: calc(100vh - 350px);
        }
        
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <header>
        <div class="container header-content">
            <a href="#" class="logo">Book<span>Nook</span></a>
            <nav>
                <ul>
                    <li><a href="#" onclick="showPage('home')">Accueil</a></li>
                    <li><a href="#" onclick="showPage('catalog')">Catalogue</a></li>
                    <li><a href="#" onclick="showPage('cart')" class="cart-icon">
                        Panier <span class="cart-count"><?php echo count($_SESSION['cart']); ?></span>
                    </a></li>
                    <li><a href="#" onclick="showPage('admin')">Admin</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <!-- Home Page -->
        <section id="home" class="page">
            <div class="hero">
                <h1>Découvrez votre prochain livre préféré</h1>
                <p>Explorez notre vaste collection de livres de fiction, non-fiction, science-fiction, mystère et biographie. Trouvez votre lecture idéale dès aujourd'hui !</p>
                <a href="#" class="btn" onclick="showPage('catalog')">Parcourir les livres</a>
            </div>

            <div class="section">
                <h2 class="section-title">Livres à la une</h2>
                <div class="books-grid">
                    <?php
                    // Fetch featured books from database
                    $stmt = $pdo->query("SELECT * FROM books LIMIT 4");
                    while ($book = $stmt->fetch(PDO::FETCH_ASSOC)): 
                        $category_stmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
                        $category_stmt->execute([$book['category_id']]);
                        $category = $category_stmt->fetchColumn();
                        $prix_fcfa = number_format($book['price'] * 655, 0, ',', ' ') . ' FCFA';
                    ?>
                    <div class="book-card">
                        <div class="book-cover">
                            <img src="https://images.unsplash.com/photo-1544947950-fa07a98d237f?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="<?php echo $book['title']; ?>">
                        </div>
                        <div class="book-details">
                            <h3 class="book-title"><?php echo $book['title']; ?></h3>
                            <p class="book-author"><?php echo $book['author']; ?></p>
                            <p class="book-category"><?php echo $category; ?></p>
                            <p class="book-price"><?php echo $prix_fcfa; ?></p>
                            <div class="book-actions">
                                <a href="#" class="btn" onclick="showBookDetail(<?php echo $book['id']; ?>)">Détails</a>
                                <a href="#" class="btn btn-accent" onclick="addToCart(<?php echo $book['id']; ?>)">Ajouter au panier</a>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </section>

        <!-- Catalog Page -->
        <section id="catalog" class="page hidden">
            <div class="section">
                <h2 class="section-title">Catalogue de livres</h2>
                
                <div class="category-nav">
                    <button class="category-btn active" data-category="all">Tous les livres</button>
                    <?php
                    // Fetch categories from database
                    $stmt = $pdo->query("SELECT * FROM categories");
                    while ($category = $stmt->fetch(PDO::FETCH_ASSOC)):
                    ?>
                    <button class="category-btn" data-category="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></button>
                    <?php endwhile; ?>
                </div>
                
                <div class="books-grid">
                    <?php
                    // Fetch all books from database
                    $stmt = $pdo->query("SELECT * FROM books");
                    while ($book = $stmt->fetch(PDO::FETCH_ASSOC)): 
                        $category_stmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
                        $category_stmt->execute([$book['category_id']]);
                        $category = $category_stmt->fetchColumn();
                        $prix_fcfa = number_format($book['price'] * 655, 0, ',', ' ') . ' FCFA';
                    ?>
                    <div class="book-card" data-category="<?php echo $book['category_id']; ?>">
                        <div class="book-cover">
                            <img src="https://images.unsplash.com/photo-1544947950-fa07a98d237f?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="<?php echo $book['title']; ?>">
                        </div>
                        <div class="book-details">
                            <h3 class="book-title"><?php echo $book['title']; ?></h3>
                            <p class="book-author"><?php echo $book['author']; ?></p>
                            <p class="book-category"><?php echo $category; ?></p>
                            <p class="book-price"><?php echo $prix_fcfa; ?></p>
                            <div class="book-actions">
                                <a href="#" class="btn" onclick="showBookDetail(<?php echo $book['id']; ?>)">Détails</a>
                                <a href="#" class="btn btn-accent" onclick="addToCart(<?php echo $book['id']; ?>)">Ajouter au panier</a>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </section>

        <!-- Book Detail Page -->
        <section id="book-detail" class="page hidden">
            <div class="section">
                <div id="book-detail-content"></div>
            </div>
        </section>

        <!-- Cart Page -->
        <section id="cart" class="page hidden">
            <div class="section">
                <h2 class="section-title">Votre panier</h2>
                
                <div class="cart-container">
                    <div id="cart-items">
                        <?php if (empty($_SESSION['cart'])): ?>
                            <p>Votre panier est vide.</p>
                        <?php else: ?>
                            <table class="cart-table">
                                <thead>
                                    <tr>
                                        <th>Livre</th>
                                        <th>Prix</th>
                                        <th>Quantité</th>
                                        <th>Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $total = 0;
                                    foreach ($_SESSION['cart'] as $id => $item):
                                        $prix_fcfa = $item['price'] * 655;
                                        $subtotal = $prix_fcfa * $item['quantity'];
                                        $total += $subtotal;
                                    ?>
                                    <tr data-id="<?php echo $id; ?>">
                                        <td>
                                            <div class="cart-item">
                                                <div class="cart-item-image">
                                                    <img src="https://images.unsplash.com/photo-1544947950-fa07a98d237f?ixlib=rb-1.2.1&auto=format&fit=crop&w=100&q=80" alt="<?php echo $item['title']; ?>">
                                                </div>
                                                <div>
                                                    <h4><?php echo $item['title']; ?></h4>
                                                    <p><?php echo $item['author']; ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo number_format($prix_fcfa, 0, ',', ' ') . ' FCFA'; ?></td>
                                        <td>
                                            <div class="quantity-control">
                                                <button class="quantity-btn" onclick="updateQuantity(<?php echo $id; ?>, -1)">-</button>
                                                <input type="number" class="quantity-input" value="<?php echo $item['quantity']; ?>" min="1" id="qty-<?php echo $id; ?>">
                                                <button class="quantity-btn" onclick="updateQuantity(<?php echo $id; ?>, 1)">+</button>
                                            </div>
                                        </td>
                                        <td><?php echo number_format($subtotal, 0, ',', ' ') . ' FCFA'; ?></td>
                                        <td>
                                            <button class="remove-btn" onclick="removeFromCart(<?php echo $id; ?>)">×</button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            
                            <div class="cart-summary">
                                <div class="summary-row">
                                    <span>Sous-total :</span>
                                    <span><?php echo number_format($total, 0, ',', ' ') . ' FCFA'; ?></span>
                                </div>
                                <div class="summary-row">
                                    <span>Livraison :</span>
                                    <span><?php echo number_format(5 * 655, 0, ',', ' ') . ' FCFA'; ?></span>
                                </div>
                                <div class="summary-row summary-total">
                                    <span>Total :</span>
                                    <span><?php echo number_format($total + (5 * 655), 0, ',', ' ') . ' FCFA'; ?></span>
                                </div>
                                <a href="#" class="btn btn-accent" style="width:100%; text-align:center;" onclick="showPage('checkout')">Passer la commande</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Checkout Page -->
        <section id="checkout" class="page hidden">
            <div class="section">
                <h2 class="section-title">Finaliser la commande</h2>
                
                <div class="cart-container">
                    <form id="checkout-form">
                        <div class="admin-form">
                            <div class="form-group">
                                <label for="name">Nom complet</label>
                                <input type="text" id="name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Adresse email</label>
                                <input type="email" id="email" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="address">Adresse de livraison</label>
                                <textarea id="address" class="form-control" rows="3" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="city">Ville</label>
                                <input type="text" id="city" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="zip">Code postal</label>
                                <input type="text" id="zip" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="country">Pays</label>
                                <select id="country" class="form-control" required>
                                    <option value="">Sélectionnez un pays</option>
                                    <option value="tg">Togo</option>
                                    <option value="fr">France</option>
                                    <option value="ci">Côte d'Ivoire</option>
                                    <option value="sn">Sénégal</option>
                                </select>
                            </div>
                            <div class="form-group form-full">
                                <h3>Informations de paiement</h3>
                            </div>
                            <div class="form-group">
                                <label for="card-name">Nom sur la carte</label>
                                <input type="text" id="card-name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="card-number">Numéro de carte</label>
                                <input type="text" id="card-number" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="exp-date">Date d'expiration</label>
                                <input type="text" id="exp-date" class="form-control" placeholder="MM/AA" required>
                            </div>
                            <div class="form-group">
                                <label for="cvv">CVV</label>
                                <input type="text" id="cvv" class="form-control" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-accent">Valider la commande</button>
                    </form>
                </div>
            </div>
        </section>

        <!-- Admin Page -->
        <section id="admin" class="page hidden">
            <div class="section">
                <h2 class="section-title">Tableau de bord Admin</h2>
                
                <div class="admin-panel">
                    <div class="admin-form">
                        <div class="form-group">
                            <label for="book-title">Titre du livre</label>
                            <input type="text" id="book-title" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="book-author">Auteur</label>
                            <input type="text" id="book-author" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="book-category">Catégorie</label>
                            <select id="book-category" class="form-control">
                                <option value="">Sélectionnez une catégorie</option>
                                <?php
                                $stmt = $pdo->query("SELECT * FROM categories");
                                while ($category = $stmt->fetch(PDO::FETCH_ASSOC)):
                                ?>
                                <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="book-price">Prix (en €)</label>
                            <input type="number" id="book-price" class="form-control" step="0.01">
                        </div>
                        <div class="form-group form-full">
                            <label for="book-description">Description</label>
                            <textarea id="book-description" class="form-control" rows="4"></textarea>
                        </div>
                        <div class="form-group form-full">
                            <button class="btn" onclick="addBook()">Ajouter un nouveau livre</button>
                        </div>
                    </div>
                    
                    <h3>Inventaire des livres</h3>
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Titre</th>
                                <th>Auteur</th>
                                <th>Catégorie</th>
                                <th>Prix</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $pdo->query("SELECT books.*, categories.name AS category_name 
                                                 FROM books 
                                                 JOIN categories ON books.category_id = categories.id");
                            while ($book = $stmt->fetch(PDO::FETCH_ASSOC)):
                                $prix_fcfa = number_format($book['price'] * 655, 0, ',', ' ') . ' FCFA';
                            ?>
                            <tr>
                                <td><?php echo $book['id']; ?></td>
                                <td><?php echo $book['title']; ?></td>
                                <td><?php echo $book['author']; ?></td>
                                <td><?php echo $book['category_name']; ?></td>
                                <td><?php echo $prix_fcfa; ?></td>
                                <td>
                                    <button class="btn" onclick="editBook(<?php echo $book['id']; ?>)">Modifier</button>
                                    <button class="btn btn-accent" onclick="deleteBook(<?php echo $book['id']; ?>)">Supprimer</button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>À propos de BookNook</h3>
                    <p>Votre destination privilégiée pour découvrir et acheter des livres dans tous les genres. Nous avons à cœur de connecter les lecteurs à leur prochain livre préféré.</p>
                </div>
                <div class="footer-section">
                    <h3>Liens utiles</h3>
                    <ul>
                        <li><a href="#" onclick="showPage('home')">Accueil</a></li>
                        <li><a href="#" onclick="showPage('catalog')">Catalogue</a></li>
                        <li><a href="#" onclick="showPage('cart')">Panier</a></li>
                        <li><a href="#" onclick="showPage('admin')">Admin</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Contact</h3>
                    <p>Email : info@booknook.com</p>
                    <p>Téléphone : (123) 456-7890</p>
                    <p>Adresse : 123 rue du Livre, Lecture, RD 12345</p>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2023 BookNook Online Bookstore. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script>
        // Show initial page
        document.addEventListener('DOMContentLoaded', function() {
            showPage('home');
            
            // Initialize category filtering
            document.querySelectorAll('.category-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const category = this.getAttribute('data-category');
                    
                    // Update active button
                    document.querySelectorAll('.category-btn').forEach(btn => {
                        btn.classList.remove('active');
                    });
                    this.classList.add('active');
                    
                    // Filter books
                    document.querySelectorAll('.book-card').forEach(card => {
                        if (category === 'all' || card.getAttribute('data-category') === category) {
                            card.style.display = 'block';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            });
        });
        
        // Show a specific page
        function showPage(pageId) {
            // Hide all pages
            document.querySelectorAll('.page').forEach(page => {
                page.classList.add('hidden');
            });
            
            // Show the selected page
            document.getElementById(pageId).classList.remove('hidden');
        }
        
        // Show book detail
        function showBookDetail(bookId) {
            // In a real application, this would fetch book details from the server
            // For this demo, we'll use static data
            document.getElementById('book-detail-content').innerHTML = `
                <div class="book-detail">
                    <div class="book-detail-image">
                        <img src="https://images.unsplash.com/photo-1544947950-fa07a98d237f?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Book Cover">
                    </div>
                    <div class="book-detail-info">
                        <h2 class="book-detail-title">Sample Book Title</h2>
                        <p class="book-detail-author">by Author Name</p>
                        <p class="book-detail-price">$19.99</p>
                        <p class="book-detail-description">
                            This is a detailed description of the book. It tells potential readers what the book is about, 
                            the main themes, and why they might enjoy reading it. A well-written description can help readers 
                            decide if this book is right for them.
                        </p>
                        <div class="book-actions">
                            <a href="#" class="btn" onclick="addToCart(${bookId})">Add to Cart</a>
                            <a href="#" class="btn btn-accent" onclick="showPage('catalog')">Back to Catalog</a>
                        </div>
                    </div>
                </div>
            `;
            showPage('book-detail');
        }
        
        // Add to cart
        function addToCart(bookId) {
            // In a real application, this would send a request to the server
            // For this demo, we'll update the session in memory
            alert(`Book #${bookId} added to cart!`);
            
            // Update cart count
            const cartCount = document.querySelector('.cart-count');
            cartCount.textContent = parseInt(cartCount.textContent) + 1;
            
            // Show cart page
            showPage('cart');
        }
        
        // Update quantity in cart
        function updateQuantity(bookId, change) {
            const input = document.getElementById(`qty-${bookId}`);
            let newQty = parseInt(input.value) + change;
            
            if (newQty < 1) newQty = 1;
            input.value = newQty;
            
            // In a real application, this would update the cart on the server
            alert(`Quantity updated for book #${bookId}`);
        }
        
        // Remove from cart
        function removeFromCart(bookId) {
            // In a real application, this would send a request to the server
            document.querySelector(`.cart-table tr[data-id="${bookId}"]`).remove();
            alert(`Book #${bookId} removed from cart`);
            
            // Update cart count
            const cartCount = document.querySelector('.cart-count');
            cartCount.textContent = parseInt(cartCount.textContent) - 1;
        }
        
        // Admin functions
        function addBook() {
            const title = document.getElementById('book-title').value;
            const author = document.getElementById('book-author').value;
            const category = document.getElementById('book-category').value;
            const price = document.getElementById('book-price').value;
            const description = document.getElementById('book-description').value;
            
            if (!title || !author || !category || !price) {
                alert('Please fill all required fields');
                return;
            }
            
            alert(`New book added: ${title} by ${author}`);
            // Reset form
            document.getElementById('book-title').value = '';
            document.getElementById('book-author').value = '';
            document.getElementById('book-category').value = '';
            document.getElementById('book-price').value = '';
            document.getElementById('book-description').value = '';
        }
        
        function editBook(bookId) {
            alert(`Editing book #${bookId}`);
            // In a real application, this would open an edit form with book details
        }
        
        function deleteBook(bookId) {
            if (confirm(`Are you sure you want to delete book #${bookId}?`)) {
                alert(`Book #${bookId} deleted`);
                // In a real application, this would remove the book from the database
            }
        }
    </script>
</body>
</html>