<?php
include 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debug pour voir les données POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log('POST Data: ' . print_r($_POST, true));
}

// Traitement ajout au panier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_to_cart') {
    $book_id = (int)$_POST['book_id'];
    $quantity = max(1, (int)$_POST['quantity']);

    // Vérifier si le livre existe déjà dans le panier
    $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE session_id = ? AND book_id = ?");
    $stmt->execute([$_SESSION['session_id'], $book_id]);
    $item = $stmt->fetch();

    if ($item) {
        // Mettre à jour la quantité
        $new_quantity = $item['quantity'] + $quantity;
        $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        $stmt->execute([$new_quantity, $item['id']]);
    } else {
        // Ajouter au panier
        $stmt = $pdo->prepare("INSERT INTO cart (session_id, book_id, quantity) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['session_id'], $book_id, $quantity]);
    }

    // Redirection pour éviter le repost
    header('Location: catalog.php?added=1');
    exit;
}

// Get categories for filter
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

// Handle filters
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';
$search_filter = isset($_GET['search']) ? $_GET['search'] : '';

// Build query
$where_conditions = [];
$params = [];

if ($category_filter) {
    $where_conditions[] = "b.category_id = ?";
    $params[] = $category_filter;
}

if ($search_filter) {
    $where_conditions[] = "(b.title LIKE ? OR b.author LIKE ?)";
    $params[] = '%' . $search_filter . '%';
    $params[] = '%' . $search_filter . '%';
}

$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}

$sql = "SELECT b.*, c.name as category_name FROM books b 
        LEFT JOIN categories c ON b.category_id = c.id 
        $where_clause 
        ORDER BY b.title";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$books = $stmt->fetchAll();



?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalogue de livres - BookStore</title>
    <!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 1.8rem;
            font-weight: bold;
        }
        
        .nav-links {
            display: flex;
            list-style: none;
            gap: 2rem;
        }
        
        .nav-links a {
            color: white;
            text-decoration: none;
            transition: opacity 0.3s;
        }
        
        .nav-links a:hover {
            opacity: 0.8;
        }
        
        .cart-icon {
            position: relative;
            background: rgba(255,255,255,0.2);
            padding: 10px 15px;
            border-radius: 25px;
            text-decoration: none;
            color: white;
        }
        
        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ff4757;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .filters {
            background: #fff;
            padding: 2rem 1.5rem;
            margin-bottom: 2rem;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(102, 126, 234, 0.08);
            max-width: 1100px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .filters-form {
            display: flex;
            gap: 1.5rem;
            align-items: flex-end;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .filter-group {
            flex: 1 1 250px;
            min-width: 220px;
        }
        
        .filter-group label {
            display: block;
            margin-bottom: 0.6rem;
            font-weight: bold;
            color: #222;
            font-size: 1.08rem;
        }
        
        .filter-group select,
        .filter-group input[type="text"] {
            width: 100%;
            padding: 12px 16px;
            border: 1.5px solid #d1d5db;
            border-radius: 10px;
            font-size: 1.08rem;
            background: #f7f9fc;
            transition: border 0.2s;
            outline: none;
        }
        
        .filter-group select:focus,
        .filter-group input[type="text"]:focus {
            border-color: #667eea;
            background: #fff;
        }
        
        .btn {
            background: #667eea;
            color: white;
            padding: 12px 28px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 1.08rem;
            font-weight: 500;
            transition: background 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.08);
        }
        
        .btn:hover {
            background: #5563c1;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: #fff;
            margin-left: 0.5rem;
        }
        
        .btn-secondary:hover {
            background: #495057;
        }
        
        .books-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .book-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }
        
        .book-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }
        
        .book-info {
            padding: 1.5rem;
        }
        
        .book-title {
            font-size: 1.1rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            color: #333;
        }
        
        .book-author {
            color: #666;
            margin-bottom: 0.5rem;
        }
        
        .book-category {
            background: #e1f5fe;
            color: #0277bd;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            display: inline-block;
            margin-bottom: 1rem;
        }
        
        .book-price {
            font-size: 1.3rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 1rem;
        }
        
        .book-stock {
            font-size: 0.9rem;
            color: #28a745;
            margin-bottom: 1rem;
        }
        
        .book-stock.low {
            color: #ffc107;
        }
        
        .book-stock.out {
            color: #dc3545;
        }
        
        .book-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .quantity-input {
            width: 60px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-align: center;
        }
        
        .alert {
            background: #4caf50;
            color: white;
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 5px;
            text-align: center;
        }
        
        .no-results {
            text-align: center;
            padding: 3rem;
            color: #666;
        }
        
        .results-count {
            margin-bottom: 1rem;
            color: #666;
        }
        
        footer {
            background: #2c3e50;
            color: white;
            text-align: center;
            padding: 2rem 0;
            margin-top: 3rem;
        }
        
        .category-filters {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        .category-btn {
            padding: 10px 24px;
            border-radius: 25px;
            border: 2px solid #2196f3;
            background: transparent;
            color: #2196f3;
            font-weight: bold;
            text-decoration: none;
            transition: background 0.2s, color 0.2s;
            font-size: 1rem;
        }
        .category-btn.active,
        .category-btn:hover {
            background: #2196f3;
            color: #fff;
            border-color: #2196f3;
        }
    </style>
</head>
<body>
    <header>
        <nav class="container">
            <div class="logo"><i class="fa-solid fa-book-open"></i> BookStore</div>
            <ul class="nav-links">
                <li><a href="index.php">Accueil</a></li>
                <li><a href="catalog.php">Livres</a></li>
                <li><a href="admin.php">Admin</a></li>
            </ul>
            <a href="cart.php" class="cart-icon">
                <i class="fa-solid fa-shopping-cart"></i> Panier
                <span class="cart-count"><?php echo getCartCount(); ?></span>
            </a>
        </nav>
    </header>

    <div class="container" style="padding-top: 2rem;">
        <?php if (isset($_GET['added'])): ?>
            <div class="alert success">
                Livre ajouté au panier avec succès !
            </div>
        <?php endif; ?>

        <div class="filters">
            <form method="GET" class="filters-form">
                <div class="filter-group">
                    <label for="category">Catégorie :</label>
                    <select name="category" id="category">
                        <option value="">Toutes les catégories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo $category_filter == $category['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="search">Recherche :</label>
                    <input type="text" name="search" id="search" placeholder="Titre ou Auteur" value="<?php echo htmlspecialchars($search_filter); ?>">
                </div>
                
                <div class="filter-group">
                    <button type="submit" class="btn">Filtrer</button>
                    <a href="catalog.php" class="btn btn-secondary">Réinitialiser</a>
                </div>
            </form>
        </div>

        <div class="category-filters">
            <a href="catalog.php" class="category-btn<?php if (!$category_filter) echo ' active'; ?>">Tous les livres</a>
            <?php foreach ($categories as $category): ?>
                <a href="catalog.php?category=<?php echo $category['id']; ?>"
                   class="category-btn<?php if ($category_filter == $category['id']) echo ' active'; ?>">
                    <?php echo htmlspecialchars($category['name']); ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="results-count">
            <?php echo count($books); ?> livre(s) trouvé(s)
        </div>

        <?php if (empty($books)): ?>
            <div class="no-results">
                <h3>Aucun livre trouvé</h3>
                <p>Essayez d'autres critères de recherche ou parcourez tous les livres.</p>
                <a href="catalog.php" class="btn">Voir tous les livres</a>
            </div>
        <?php else: ?>
            <div class="books-grid">
                <?php foreach ($books as $book): ?>
                    <div class="book-card">
                        <img src="<?php echo htmlspecialchars($book['image_url']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>" class="book-image">
                        <div class="book-info">
                            <h3 class="book-title"><?php echo htmlspecialchars($book['title']); ?></h3>
                            <p class="book-author">par <?php echo htmlspecialchars($book['author']); ?></p>
                            <span class="book-category"><?php echo htmlspecialchars($book['category_name']); ?></span>
                            <div class="book-price"><?php echo formatPriceFCFA($book['price']); ?></div>
                            
                            <?php if ($book['stock_quantity'] > 10): ?>
                                <div class="book-stock">En stock (<?php echo $book['stock_quantity']; ?>)</div>
                            <?php elseif ($book['stock_quantity'] > 0): ?>
                                <div class="book-stock low">Stock faible (<?php echo $book['stock_quantity']; ?>)</div>
                            <?php else: ?>
                                <div class="book-stock out">Rupture de stock</div>
                            <?php endif; ?>
                            
                            <?php if ($book['stock_quantity'] > 0): ?>
                                <form method="POST" class="book-actions">
                                    <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                    <input type="hidden" name="action" value="add_to_cart">
                                    <input type="number" name="quantity" value="1" min="1" max="<?php echo $book['stock_quantity']; ?>" class="quantity-input">
                                    <button type="submit" name="add_to_cart" class="btn">
                                        <i class="fa-solid fa-shopping-cart"></i> Panier
                                    </button>
                                </form>
                            <?php endif; ?>
                            
                            <a href="book.php?id=<?php echo $book['id']; ?>" class="btn btn-secondary" style="margin-top: 10px; display: inline-block;">Voir les détails</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pied de page commun -->
<footer style="background: #33475b; color: #fff; padding: 3rem 0 1.5rem 0; margin-top: 3rem;">
    <div style="max-width: 1200px; margin: auto; display: flex; flex-wrap: wrap; gap: 2rem; justify-content: space-between;">
        <div style="flex: 1 1 250px; min-width: 220px;">
            <h2 style="font-size: 1.5rem; margin-bottom: 0.5rem; border-bottom: 3px solid #5ec6fa; display: inline-block; padding-bottom: 0.2rem;">À propos de BookStore</h2>
            <p style="margin-top: 1rem;">
                Votre destination privilégiée pour découvrir et acheter des livres dans tous les genres. Nous avons à cœur de connecter les lecteurs à leur prochain livre préféré.
            </p>
        </div>
        <div style="flex: 1 1 180px; min-width: 180px;">
            <h2 style="font-size: 1.5rem; margin-bottom: 0.5rem; border-bottom: 3px solid #5ec6fa; display: inline-block; padding-bottom: 0.2rem;">Liens utiles</h2>
            <ul style="list-style: none; padding: 0; margin-top: 1rem;">
                <li><a href="index.php" style="color: #fff; text-decoration: underline;">Accueil</a></li>
                <li><a href="catalog.php" style="color: #fff; text-decoration: underline;">Catalogue</a></li>
                <li><a href="cart.php" style="color: #fff; text-decoration: underline;">Panier</a></li>
                <li><a href="admin.php" style="color: #fff; text-decoration: underline;">Admin</a></li>
            </ul>
        </div>
        <div style="flex: 1 1 250px; min-width: 220px;">
            <h2 style="font-size: 1.5rem; margin-bottom: 0.5rem; border-bottom: 3px solid #5ec6fa; display: inline-block; padding-bottom: 0.2rem;">Contact</h2>
            <ul style="list-style: none; padding: 0; margin-top: 1rem;">
                <li>Email : marshallironside7@gmail.com</li>
                <li>Téléphone : +228 70 09 74 54</li>
                <li>Adresse : Avédji-Lomé TOGO</li>
            </ul>
        </div>
    </div>
    <hr style="border: none; border-top: 1px solid #46607a; margin: 2rem 0 1rem 0;">
    <div style="text-align: center; color: #cfd8dc;">
        &copy; 2025 BookStore. Tous droits réservés.
    </div>
</footer>

    <script>
        // Soumission automatique du formulaire au changement de catégorie
        document.getElementById('category').addEventListener('change', function() {
            this.form.submit();
        });

        // Animation du panier
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const btn = this.querySelector('button[name="add_to_cart"]');
                if (btn) {
                    btn.textContent = 'Ajout...';
                    btn.disabled = true;
                }
            });
        });
    </script>
</body>
</html>