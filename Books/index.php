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
    <!-- CSS externe -->
    <link rel="stylesheet" href="styles.css">
</head>
<body class="home">
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
    <?php include 'footer.php'; ?>

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