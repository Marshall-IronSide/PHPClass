<?php
include 'config.php';

$debug_info = [];

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
   header('Location: book.php?id=' . $book_id . '&added=1');
    exit;
}

// Get book ID
$book_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$book_id) {
    header('Location: catalog.php');
    exit;
}

// Get book details
$stmt = $pdo->prepare("SELECT b.*, c.name as category_name FROM books b 
                       LEFT JOIN categories c ON b.category_id = c.id 
                       WHERE b.id = ?");
$stmt->execute([$book_id]);
$book = $stmt->fetch();

if (!$book) {
    header('Location: catalog.php');
    exit;
}

// Get related books (same category)
$stmt = $pdo->prepare("SELECT b.*, c.name as category_name FROM books b 
                       LEFT JOIN categories c ON b.category_id = c.id 
                       WHERE b.category_id = ? AND b.id != ? 
                       ORDER BY RAND() LIMIT 4");
$stmt->execute([$book['category_id'], $book_id]);
$related_books = $stmt->fetchAll();

// Traduction des catégories
function traduireCategorie($cat) {
    $traductions = [
        'Fiction' => 'Fiction',
        'Science Fiction' => 'Science-fiction',
        'Romance' => 'Romance',
        'Fantasy' => 'Fantastique',
        'History' => 'Histoire',
        'Biography' => 'Biographie',
        'Children' => 'Jeunesse',
        'Thriller' => 'Thriller',
        'Mystery' => 'Mystère',
        'Self-help' => 'Développement personnel',
        // Ajoute d'autres traductions si besoin
    ];
    return $traductions[$cat] ?? $cat;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($book['title']); ?> - BookStore</title>
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
        
        .breadcrumb {
            padding: 1rem 0;
            color: #666;
        }
        
        .breadcrumb a {
            color: #667eea;
            text-decoration: none;
        }
        
        .book-detail {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin: 2rem 0;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .book-main {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 3rem;
            margin-bottom: 3rem;
        }
        
        .book-image {
            width: 100%;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .book-info h1 {
            font-size: 2.2rem;
            margin-bottom: 1rem;
            color: #333;
        }
        
        .book-author {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 1rem;
        }
        
        .book-category {
            background: #e1f5fe;
            color: #0277bd;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            display: inline-block;
            margin-bottom: 1.5rem;
        }
        
        .book-price {
            font-size: 2rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 1rem;
        }
        
        .book-stock {
            font-size: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .stock-available {
            color: #28a745;
        }
        
        .stock-low {
            color: #ffc107;
        }
        
        .stock-out {
            color: #dc3545;
        }
        
        .book-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 10px;
        }
        
        .meta-item {
            text-align: center;
        }
        
        .meta-label {
            font-weight: bold;
            color: #666;
            font-size: 0.9rem;
        }
        
        .meta-value {
            font-size: 1.1rem;
            color: #333;
        }
        
        .book-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .quantity-input {
            width: 80px;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            text-align: center;
            font-size: 16px;
        }
        
        .btn {
            background: #667eea;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
            font-size: 16px;
        }
        
        .btn:hover {
            background: #5a6fd8;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #6c757d;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .description {
            margin-bottom: 2rem;
        }
        
        .description h3 {
            margin-bottom: 1rem;
            color: #333;
        }
        
        .related-books {
            margin-top: 3rem;
        }
        
        .section-title {
            font-size: 1.8rem;
            margin-bottom: 2rem;
            color: #333;
            text-align: center;
        }
        
        .books-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }
        
        .book-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .book-card:hover {
            transform: translateY(-5px);
        }
        
        .book-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .book-card-info {
            padding: 1rem;
        }
        
        .book-card-title {
            font-size: 1rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .book-card-author {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        .book-card-price {
            font-size: 1.2rem;
            font-weight: bold;
            color: #667eea;
        }
        
        .alert {
            background: #4caf50;
            color: white;
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 5px;
            text-align: center;
        }
        
        .alert-error {
            background: #f44336;
        }
        
        .alert-debug {
            background: #ff9800;
            color: #333;
            text-align: left;
            font-family: monospace;
            font-size: 12px;
        }
        
        .out-of-stock {
            color: #dc3545;
            font-weight: bold;
            font-size: 1.1rem;
        }
        
        /* CORRECTION POUR LE FOOTER - Ajoutez ceci à votre styles2.css */

/* ===== FOOTER STYLES ===== */
footer {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    color: #ffffff !important;
    padding: 3rem 0 1rem 0 !important;
    margin-top: 4rem !important;
}

footer h2 {
    color: #ffffff !important;
    font-size: 1.2rem !important;
    margin-bottom: 1rem !important;
    border-bottom: 2px solid rgba(255,255,255,0.2) !important;
    padding-bottom: 0.5rem !important;
}

footer p {
    color: #e8eaf6 !important;
    line-height: 1.6 !important;
    margin-top: 1rem !important;
}

footer ul {
    list-style: none !important;
    padding: 0 !important;
    margin: 1rem 0 !important;
}

footer ul li {
    margin-bottom: 0.5rem !important;
}

footer ul li a {
    color: #e8eaf6 !important;
    text-decoration: none !important;
    transition: color 0.3s ease !important;
}

footer ul li a:hover {
    color: #ffffff !important;
    text-decoration: underline !important;
}

/* Ligne de séparation dans le footer */
footer hr {
    border: none !important;
    border-top: 1px solid rgba(255,255,255,0.2) !important;
    margin: 2rem 0 1rem 0 !important;
}

/* Copyright text */
footer div[style*="text-align: center"] {
    color: #cfd8dc !important;
    font-size: 0.9rem !important;
}

/* Container du footer */
footer .container {
    max-width: 1200px !important;
    margin: 0 auto !important;
    padding: 0 20px !important;
}

/* Flexbox layout pour le footer */
footer .container > div[style*="display: flex"] {
    display: flex !important;
    flex-wrap: wrap !important;
    gap: 2rem !important;
    justify-content: space-between !important;
}

footer .container > div[style*="display: flex"] > div {
    flex: 1 1 250px !important;
    min-width: 220px !important;
}

/* Responsive pour le footer */
@media (max-width: 768px) {
    footer {
        padding: 2rem 0 1rem 0 !important;
    }
    
    footer .container > div[style*="display: flex"] {
        flex-direction: column !important;
        gap: 1.5rem !important;
    }
    
    footer .container > div[style*="display: flex"] > div {
        min-width: auto !important;
    }
    
    footer h2 {
        font-size: 1.1rem !important;
    }
}
        
        @media (max-width: 768px) {
            .book-main {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            
            .book-info h1 {
                font-size: 1.8rem;
            }
            
            .book-actions {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
</head>
<body>
    <header>
        <nav class="container">
            <a href="index.php" class="logo" style="text-decoration:none; color:inherit;">
                <i class="fa-solid fa-book-open"></i> BookStore
            </a>
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

    <div class="container">
        <div class="breadcrumb">
            <a href="index.php">Accueil</a> →
            <a href="catalog.php">Livres</a> →
            <a href="catalog.php?category=<?php echo $book['category_id']; ?>"><?php echo htmlspecialchars($book['category_name']); ?></a> →
            <?php echo htmlspecialchars($book['title']); ?>
        </div>

        <?php if (isset($_GET['added'])): ?>
            <div class="alert">Livre ajouté au panier avec succès !</div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (!empty($debug_info) && isset($_GET['debug'])): ?>
            <div class="alert alert-debug">
                <strong>Debug Info:</strong><br>
                <?php foreach ($debug_info as $info): ?>
                    <?php echo htmlspecialchars($info); ?><br>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="book-detail">
            <div class="book-main">
                <div>
                    <img src="<?php echo htmlspecialchars($book['image_url']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>" class="book-image">
                </div>
                <div class="book-info">
                    <h1><?php echo htmlspecialchars($book['title']); ?></h1>
                    <p class="book-author">par <?php echo htmlspecialchars($book['author']); ?></p>
                    <span class="book-category"><?php echo traduireCategorie($book['category_name']); ?></span>
                    
                    <div class="book-price"><?php echo formatPriceFCFA($book['price']); ?></div>
                    
                    <div class="book-stock <?php echo $book['stock_quantity'] > 10 ? 'stock-available' : ($book['stock_quantity'] > 0 ? 'stock-low' : 'stock-out'); ?>">
                        <?php if ($book['stock_quantity'] > 10): ?>
                            ✅ En stock (<?php echo $book['stock_quantity']; ?> disponibles)
                        <?php elseif ($book['stock_quantity'] > 0): ?>
                            ⚠️ Plus que <?php echo $book['stock_quantity']; ?> en stock
                        <?php else: ?>
                            ❌ Rupture de stock
                        <?php endif; ?>
                    </div>
                    
                    <div class="book-meta">
                        <div class="meta-item">
                            <div class="meta-label">ISBN</div>
                            <div class="meta-value"><?php echo htmlspecialchars($book['isbn']); ?></div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-label">Catégorie</div>
                            <div class="meta-value"><?php echo traduireCategorie($book['category_name']); ?></div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-label">Prix</div>
                            <div class="meta-value"><?php echo formatPriceFCFA($book['price']); ?></div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-label">Stock</div>
                            <div class="meta-value"><?php echo $book['stock_quantity']; ?></div>
                        </div>
                    </div>
                    
                    <?php if ($book['stock_quantity'] > 0): ?>
                        <form method="POST" class="book-actions">
                            <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                            <input type="hidden" name="action" value="add_to_cart"> <!-- AJOUTE CETTE LIGNE -->
                            <input type="number" name="quantity" value="1" min="1" max="<?php echo $book['stock_quantity']; ?>" class="quantity-input">
                            <button type="submit" name="add_to_cart" class="btn">
                                <i class="fa-solid fa-shopping-cart"></i> Ajouter au panier
                            </button>
                        </form>
                    <?php else: ?>
                        <p class="out-of-stock">Rupture de stock</p>
                    <?php endif; ?>
                    
                    <a href="catalog.php" class="btn btn-secondary">← Retour au catalogue</a>
                </div>
            </div>
            
            <?php if ($book['description']): ?>
                <div class="description">
                    <h3>Description</h3>
                    <p><?php echo nl2br(htmlspecialchars($book['description'])); ?></p>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($related_books)): ?>
            <div class="related-books">
                <h2 class="section-title">Livres similaires</h2>
                <div class="books-grid">
                    <?php foreach ($related_books as $related_book): ?>
                        <div class="book-card">
                            <img src="<?php echo htmlspecialchars($related_book['image_url']); ?>" alt="<?php echo htmlspecialchars($related_book['title']); ?>">
                            <div class="book-card-info">
                                <h4 class="book-card-title"><?php echo htmlspecialchars($related_book['title']); ?></h4>
                                <p class="book-card-author">par <?php echo htmlspecialchars($related_book['author']); ?></p>
                                <div class="book-card-price"><?php echo formatPriceFCFA($related_book['price']); ?></div>
                                <a href="book.php?id=<?php echo $related_book['id']; ?>" class="btn" style="margin-top: 1rem; font-size: 14px;">Voir les détails</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
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
        // Animation ajout au panier
        document.querySelector('form.book-actions')?.addEventListener('submit', function(e) {
            const btn = this.querySelector('button[name="add_to_cart"]');
            if (btn) {
                btn.innerHTML = '<i class="fa-solid fa-shopping-cart"></i> Ajout...';
                btn.disabled = true;
            }
        });
    </script>
</body>
</html>