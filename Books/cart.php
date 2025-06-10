<?php
ob_start();
include 'config.php';

// Créer une session_id si besoin
if (!isset($_SESSION['session_id'])) {
    $_SESSION['session_id'] = session_id();
}

// Actions panier
if (isset($_POST['action'])) {
    if ($_POST['action'] === 'update') {
        $cart_id = (int)$_POST['cart_id'];
        $quantity = (int)$_POST['quantity'];
        if ($quantity > 0) {
            $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND session_id = ?");
            $stmt->execute([$quantity, $cart_id, $_SESSION['session_id']]);
        } else {
            $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND session_id = ?");
            $stmt->execute([$cart_id, $_SESSION['session_id']]);
        }
        ob_end_clean();
        header('Location: cart.php?updated=1');
        exit;
    } elseif ($_POST['action'] === 'remove') {
        $cart_id = (int)$_POST['cart_id'];
        $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND session_id = ?");
        $stmt->execute([$cart_id, $_SESSION['session_id']]);
        ob_end_clean();
        header('Location: cart.php?removed=1');
        exit;
    } elseif ($_POST['action'] === 'clear') {
        $stmt = $pdo->prepare("DELETE FROM cart WHERE session_id = ?");
        $stmt->execute([$_SESSION['session_id']]);
        ob_end_clean();
        header('Location: cart.php?cleared=1');
        exit;
    }
}

// Checkout
if (isset($_POST['checkout'])) {
    $customer_name = trim($_POST['customer_name']);
    $customer_email = trim($_POST['customer_email']);
    $customer_phone = trim($_POST['customer_phone']);
    $shipping_address = trim($_POST['shipping_address']);

    if (empty($customer_name) || empty($customer_email) || empty($shipping_address)) {
        $error = "Veuillez remplir tous les champs obligatoires.";
    } else {
        $stmt = $pdo->prepare("SELECT c.*, b.title, b.price, b.stock_quantity 
                               FROM cart c 
                               JOIN books b ON c.book_id = b.id 
                               WHERE c.session_id = ?");
        $stmt->execute([$_SESSION['session_id']]);
        $cart_items = $stmt->fetchAll();

        if (empty($cart_items)) {
            $error = "Votre panier est vide.";
        } else {
            $total_amount = 0;
            $valid_order = true;
            foreach ($cart_items as $item) {
                if ($item['quantity'] > $item['stock_quantity']) {
                    $valid_order = false;
                    break;
                }
                $total_amount += $item['price'] * $item['quantity'];
            }
            if (!$valid_order) {
                $error = "Certains articles ne sont plus disponibles en quantité suffisante.";
            } else {
                try {
                    $pdo->beginTransaction();
                    $stmt = $pdo->prepare("INSERT INTO orders (customer_name, customer_email, customer_phone, shipping_address, total_amount, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                    $stmt->execute([$customer_name, $customer_email, $customer_phone, $shipping_address, $total_amount]);
                    $order_id = $pdo->lastInsertId();
                    foreach ($cart_items as $item) {
                        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, book_id, quantity, price) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$order_id, $item['book_id'], $item['quantity'], $item['price']]);
                        $stmt = $pdo->prepare("UPDATE books SET stock_quantity = stock_quantity - ? WHERE id = ?");
                        $stmt->execute([$item['quantity'], $item['book_id']]);
                    }
                    $stmt = $pdo->prepare("DELETE FROM cart WHERE session_id = ?");
                    $stmt->execute([$_SESSION['session_id']]);
                    $pdo->commit();
                    $success_message = "Paiement effectué !";
                    $cart_items = [];
                } catch (Exception $e) {
                    $pdo->rollBack();
                    $error = "Erreur lors du traitement de la commande: " . $e->getMessage();
                }
            }
        }
    }
}

// Récupérer les articles du panier pour affichage
$stmt = $pdo->prepare("SELECT c.*, b.title, b.author, b.price, b.image_url, b.stock_quantity 
                       FROM cart c 
                       JOIN books b ON c.book_id = b.id 
                       WHERE c.session_id = ? 
                       ORDER BY c.created_at DESC");
$stmt->execute([$_SESSION['session_id']]);
$cart_items = $cart_items ?? $stmt->fetchAll();


?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - BookStore</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="styles2.css">
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

    <div class="container main-content">
        <h1 class="page-title">Mon panier</h1>

        <?php if (isset($success_message)): ?>
            <div class="alert success"><?php echo $success_message; ?></div>
        <?php else: ?>
            <?php if (isset($_GET['updated'])): ?>
                <div class="alert success">Panier mis à jour avec succès !</div>
            <?php endif; ?>
            <?php if (isset($_GET['removed'])): ?>
                <div class="alert info">Article retiré du panier !</div>
            <?php endif; ?>
            <?php if (isset($_GET['cleared'])): ?>
                <div class="alert info">Panier vidé avec succès !</div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (empty($cart_items)): ?>
            <div class="empty-cart">
                <h3>Votre panier est vide</h3>
                <p>Commencez vos achats pour ajouter des articles à votre panier.</p>
                <a href="catalog.php" class="btn">Voir le catalogue</a>
            </div>
        <?php else: ?>
            <div class="cart-container">
                <div class="cart-items">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <h3>Articles du panier (<?php echo count($cart_items); ?>)</h3>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="clear">
                            <button type="submit" class="btn btn-warning btn-small" onclick="return confirm('Vider tout le panier ?')">Vider le panier</button>
                        </form>
                    </div>
                    <?php foreach ($cart_items as $item): ?>
                        <div class="cart-item">
                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($item['title']); ?>" 
                                 class="item-image">
                            <div class="item-details">
                                <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                                <p>par <?php echo htmlspecialchars($item['author']); ?></p>
                                <?php if ($item['quantity'] > $item['stock_quantity']): ?>
                                    <p style="color: #dc3545; font-weight: bold;">⚠️ Il n'en reste que <?php echo $item['stock_quantity']; ?> en stock</p>
                                <?php endif; ?>
                            </div>
                            <div class="item-price">
                                <?php echo formatPriceFCFA($item['price']); ?>
                            </div>
                            <div class="quantity-controls">
                                <form method="POST" style="display: flex; align-items: center; gap: 0.5rem;">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" 
                                           min="1" max="<?php echo $item['stock_quantity']; ?>" 
                                           class="quantity-input" onchange="this.form.submit()">
                                </form>
                            </div>
                            <div class="item-actions">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-small" 
                                            onclick="return confirm('Retirer cet article ?')">Retirer</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="checkout-section">
                    <div class="order-summary">
                        <h3>Résumé de la commande</h3>
                        <?php 
                        $subtotal = 0;
                        $item_count = 0;
                        foreach ($cart_items as $item) {
                            $item_total = $item['price'] * $item['quantity'];
                            $subtotal += $item_total;
                            $item_count += $item['quantity'];
                        }
                        $shipping = $subtotal > 750 ? 0 : 100;
                        $tax = $subtotal * 0.08;
                        $total = $subtotal + $shipping + $tax;
                        ?>
                        <div class="summary-item">
                            <span>Articles (<?php echo $item_count; ?>) :</span>
                            <span><?php echo formatPriceFCFA($subtotal); ?></span>
                        </div>
                        <div class="summary-item">
                            <span>Livraison :</span>
                            <span><?php echo $shipping > 0 ? formatPriceFCFA($shipping) : 'GRATUIT'; ?></span>
                        </div>
                        <div class="summary-item">
                            <span>Taxe :</span>
                            <span><?php echo formatPriceFCFA($tax); ?></span>
                        </div>
                        <div class="summary-item summary-total">
                            <span>Total :</span>
                            <span><?php echo formatPriceFCFA($total); ?></span>
                        </div>
                        <?php if ($shipping > 0): ?>
                            <p style="font-size: 0.9rem; color: #666; margin-top: 0.5rem;">
                                🚚 Livraison gratuite à partir de 750 FCFA
                            </p>
                        <?php endif; ?>
                    </div>
                    <form method="POST" action="cart.php" class="checkout-form">
                        <h3>Informations de livraison</h3>
                        <div class="form-group">
                            <label for="customer_name">Nom complet *</label>
                            <input type="text" id="customer_name" name="customer_name" required>
                        </div>
                        <div class="form-group">
                            <label for="customer_email">Adresse email *</label>
                            <input type="email" id="customer_email" name="customer_email" required>
                        </div>
                        <div class="form-group">
                            <label for="customer_phone">Téléphone</label>
                            <input type="tel" id="customer_phone" name="customer_phone">
                        </div>
                        <div class="form-group">
                            <label for="shipping_address">Adresse de livraison *</label>
                            <textarea id="shipping_address" name="shipping_address" required 
                                      placeholder="Saisissez votre adresse complète"></textarea>
                        </div>
                        <div class="form-group payment-group">
                            <label for="card_number">Numéro de carte</label>
                            <div class="input-icon-group">
                                <input type="text" id="card_number" name="card_number" maxlength="19" placeholder="1234 1234 1234 1234" required pattern="\d{4} \d{4} \d{4} \d{4}">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/4/41/Visa_Logo.png" alt="VISA" class="card-icon" style="height:22px; margin-left:8px;">
                            </div>
                        </div>
                        <div style="display: flex; gap: 1rem;">
                            <div class="form-group" style="flex:1;">
                                <label for="exp_date">Date d'expiration</label>
                                <input type="text" id="exp_date" name="exp_date" maxlength="7" placeholder="MM / AA" required pattern="\d{2} / \d{2}">
                            </div>
                            <div class="form-group" style="flex:1;">
                                <label for="cvc">Code de sécurité</label>
                                <div class="input-icon-group">
                                    <input type="text" id="cvc" name="cvc" maxlength="4" placeholder="CVC" required pattern="\d{3,4}">
                                    <span class="cvc-icon" style="margin-left:8px; display:flex; align-items:center;">
                                        <svg width="28" height="18" viewBox="0 0 28 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect x="1" y="1" width="26" height="16" rx="3" stroke="#aaa" stroke-width="2" fill="none"/>
                                            <text x="14" y="13" text-anchor="middle" fill="#aaa" font-size="10" font-family="Arial">123</text>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <button type="submit" name="checkout" class="btn" style="width: 100%; padding: 15px; font-size: 18px;">
                            🛒 Valider la commande - <?php echo formatPriceFCFA($total); ?>
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <footer>
        <div class="container">
            <div style="display: flex; flex-wrap: wrap; gap: 2rem; justify-content: space-between;">
                <div style="flex: 1 1 250px; min-width: 220px;">
                    <h2>À propos de BookStore</h2>
                    <p style="margin-top: 1rem;">
                        Votre destination privilégiée pour découvrir et acheter des livres dans tous les genres. Nous avons à cœur de connecter les lecteurs à leur prochain livre préféré.
                    </p>
                </div>
                <div style="flex: 1 1 180px; min-width: 180px;">
                    <h2>Liens utiles</h2>
                    <ul>
                        <li><a href="index.php">Accueil</a></li>
                        <li><a href="catalog.php">Catalogue</a></li>
                        <li><a href="cart.php">Panier</a></li>
                        <li><a href="admin.php">Admin</a></li>
                    </ul>
                </div>
                <div style="flex: 1 1 250px; min-width: 220px;">
                    <h2>Contact</h2>
                    <ul>
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
        </div>
    </footer>

    <script>
        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', function() {
                if (this.value < 1) this.value = 1;
            });
        });

        document.querySelector('.checkout-form')?.addEventListener('submit', function(e) {
            const name = document.getElementById('customer_name').value.trim();
            const email = document.getElementById('customer_email').value.trim();
            const address = document.getElementById('shipping_address').value.trim();
            if (!name || !email || !address) {
                alert('Veuillez remplir tous les champs obligatoires');
                e.preventDefault();
                return false;
            }
            const btn = this.querySelector('button[name="checkout"]');
            if (btn) {
                btn.textContent = '⏳ Traitement en cours...';
                btn.disabled = true;
            }
        });

        document.querySelectorAll('.alert').forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            }, 5000);
        });
    </script>
</body>
</html>