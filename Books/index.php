<?php
include 'config.php';

// Création d'un identifiant de session unique si besoin
if (!isset($_SESSION['session_id'])) {
    $_SESSION['session_id'] = session_create_id();
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
    header('Location: index.php?added=1');
    exit;
}

// Get featured books (showing 6 books regardless of featured status if not enough featured books)
$stmt = $pdo->query("SELECT b.*, c.name as category_name FROM books b 
                     LEFT JOIN categories c ON b.category_id = c.id 
                     ORDER BY b.featured DESC, b.created_at DESC LIMIT 8
                     ");
$featured_books = $stmt->fetchAll();


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookStore - Votre librairie en ligne</title>
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
        
        .hero {
            background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('https://images.unsplash.com/photo-1481627834876-b7833e8f5570?ixlib=rb-4.0.3') center/cover;
            color: white;
            text-align: center;
            padding: 100px 0;
        }
        
        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }
        
        .btn {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 25px;
            transition: transform 0.3s, box-shadow 0.3s;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .section {
            padding: 60px 0;
            background: #f3f6fa; /* Bleu très clair qui contraste avec les box */
        }
        
        .section-title {
            text-align: center;
            font-size: 2.7rem;
            font-weight: 700;
            color: #222;
            letter-spacing: 1px;
            margin-bottom: 2.5rem;
            position: relative;
            font-family: 'Segoe UI', 'Montserrat', Arial, sans-serif;
        }
        
        .section-title::after {
            content: "";
            display: block;
            margin: 18px auto 0 auto;
            width: 80px;
            height: 5px;
            border-radius: 3px;
            background: linear-gradient(90deg, #667eea 0%, #5ec6fa 100%);
            opacity: 0.7;
        }
        
        .books-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
            margin-top: 2rem;
            background: none;
            border-radius: 18px;
            padding: 30px 0;
        }
        @media (max-width: 1100px) {
            .books-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 700px) {
            .books-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .book-card {
            background: #fff;
            border-radius: 18px; /* arrondi tous les coins */
            box-shadow: 0 4px 24px rgba(102, 126, 234, 0.10), 0 1.5px 8px rgba(51,71,91,0.08);
            border: 1.5px solid #e3e8f0;
            transition: transform 0.2s, box-shadow 0.2s;
            display: flex;
            flex-direction: column;
            align-items: stretch;
            min-height: 480px;
            overflow: hidden; /* pour que l'image suive l'arrondi */
        }
        
        .book-card:hover {
            transform: translateY(-6px) scale(1.03);
            box-shadow: 0 8px 32px rgba(102, 126, 234, 0.18);
        }
        
        .book-image {
            width: 100%;
            height: 320px;
            object-fit: cover;
            object-position: center;
            background: #f4f4f4;
            border-bottom: 1px solid #eee;
            display: block;
            border-top-left-radius: 18px;
            border-top-right-radius: 18px;
        }
        
        .book-info {
            flex: 1;
            padding: 1.2rem 1.2rem 1.5rem 1.2rem;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }
        
        .book-title {
            font-size: 1.08rem;
            font-weight: bold;
            margin-bottom: 0.3rem;
            color: #222;
        }
        
        .book-author {
            color: #666;
            font-size: 0.98rem;
            margin-bottom: 0.5rem;
        }
        
        .book-category {
            background: #e1f5fe;
            color: #0277bd;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.85rem;
            display: inline-block;
            margin-bottom: 1rem;
        }
        
        .book-price {
            font-size: 1.25rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 1rem;
        }
        
        .book-actions {
            display: flex;
            gap: 8px;
            align-items: center;
            margin-top: 0.5rem;
            padding: 0;
            background: none;
            box-shadow: none;
        }
        
        .book-actions .btn {
            border-radius: 6px;
            padding: 12px 0;
            font-size: 1rem;
            background: #667eea;
            color: #fff;
            border: none;
            box-shadow: none;
            font-weight: 500;
            transition: background 0.2s;
            flex: 1 1 0;
            min-width: 0;
            text-align: center;
            width: 100%;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .book-actions .btn:last-child {
            background: #495057;
            margin-left: 0;
        }
        
        .book-actions .btn:hover {
            background: #5563c1;
        }
        
        .book-actions .btn:last-child:hover {
            background: #222;
        }
        
        .book-actions .quantity-input {
            border-radius: 6px;
            border: 1px solid #ddd;
            width: 100%;
            height: 48px;
            font-size: 1rem;
            text-align: center;
            flex: 1 1 0;
            min-width: 0;
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            transition: border 0.2s;
        }
        
        footer {
            background: #33475b;
            color: #fff;
            padding: 3rem 0 1.5rem 0;
            margin-top: 3rem;
        }
        
        footer h2 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            border-bottom: 3px solid #5ec6fa;
            display: inline-block;
            padding-bottom: 0.2rem;
        }
        
        footer ul {
            list-style: none;
            padding: 0;
            margin-top: 1rem;
        }
        
        footer ul li {
            margin-bottom: 0.5rem;
        }
        
        .alert {
            background: #4caf50;
            color: white;
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 5px;
            text-align: center;
        }
        
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }
            
            .hero h1 {
                font-size: 2rem;
            }
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

    <section class="hero">
        <div class="container">
            <h1>Bienvenue sur BookStore</h1>
            <p>
                Explorez notre vaste collection de livres de fiction, non-fiction, science-fiction, mystère et biographie. Trouvez dès aujourd'hui votre lecture idéale !
            </p>
            <a href="catalog.php" class="btn">Parcourir les livres</a>
        </div>
    </section>

    <?php if (isset($_GET['added'])): ?>
        <div class="alert success">Livre ajouté au panier !</div>
    <?php endif; ?>

    <section class="section">
        <div class="container">
            <h2 class="section-title">Livres à la une</h2>
            <div class="books-grid">
                <?php foreach ($featured_books as $book): ?>
                    <div class="book-card">
                        <img src="<?php echo htmlspecialchars($book['image_url']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>" class="book-image">
                        <div class="book-info">
                            <h3 class="book-title"><?php echo htmlspecialchars($book['title']); ?></h3>
                            <p class="book-author">par <?php echo htmlspecialchars($book['author']); ?></p>
                            <span class="book-category"><?php echo htmlspecialchars($book['category_name']); ?></span>
                            <div class="book-price"><?php echo formatPriceFCFA($book['price']); ?></div>
                            <form method="POST" class="book-actions">
                                <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                <input type="hidden" name="action" value="add_to_cart">
                                <input type="number" name="quantity" value="1" min="1" max="<?php echo $book['stock_quantity']; ?>" class="quantity-input">
                                <button type="submit" class="btn"><i class="fa-solid fa-shopping-cart"></i> Panier</button>
                                <a href="book.php?id=<?php echo $book['id']; ?>" class="btn">Détails</a>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

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
        // Animation simple du panier
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
