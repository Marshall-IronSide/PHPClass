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
    <!-- CSS externe -->
    <link rel="stylesheet" href="styles.css">
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
    <?php include 'footer.php'; ?>
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