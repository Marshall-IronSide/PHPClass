<?php
include 'config.php';

// Handle cart actions
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
        
        header('Location: cart.php?updated=1');
        exit;
    } elseif ($_POST['action'] === 'remove') {
        $cart_id = (int)$_POST['cart_id'];
        $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND session_id = ?");
        $stmt->execute([$cart_id, $_SESSION['session_id']]);
        
        header('Location: cart.php?removed=1');
        exit;
    } elseif ($_POST['action'] === 'clear') {
        $stmt = $pdo->prepare("DELETE FROM cart WHERE session_id = ?");
        $stmt->execute([$_SESSION['session_id']]);
        
        header('Location: cart.php?cleared=1');
        exit;
    }
}

// Handle checkout
if (isset($_POST['checkout'])) {
    $customer_name = trim($_POST['customer_name']);
    $customer_email = trim($_POST['customer_email']);
    $customer_phone = trim($_POST['customer_phone']);
    $shipping_address = trim($_POST['shipping_address']);
    
    // Debug: Log checkout attempt
    error_log("Checkout attempt - Name: $customer_name, Email: $customer_email");
    
    if ($customer_name && $customer_email && $shipping_address) {
        // Get cart items
        $stmt = $pdo->prepare("SELECT c.*, b.title, b.price, b.stock_quantity 
                               FROM cart c 
                               JOIN books b ON c.book_id = b.id 
                               WHERE c.session_id = ?");
        $stmt->execute([$_SESSION['session_id']]);
        $cart_items = $stmt->fetchAll();
        
        if (!empty($cart_items)) {
            $total_amount = 0;
            $valid_order = true;
            
            // Check stock and calculate total
            foreach ($cart_items as $item) {
                if ($item['quantity'] > $item['stock_quantity']) {
                    $valid_order = false;
                    break;
                }
                $total_amount += $item['price'] * $item['quantity'];
            }
            
            if ($valid_order) {
                try {
                    $pdo->beginTransaction();
                    
                    // Create order
                    $stmt = $pdo->prepare("INSERT INTO orders (customer_name, customer_email, customer_phone, shipping_address, total_amount) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$customer_name, $customer_email, $customer_phone, $shipping_address, $total_amount]);
                    $order_id = $pdo->lastInsertId();
                    
                    // Debug: Log order creation
                    error_log("Order created with ID: $order_id");
                    
                    // Add order items and update stock
                    foreach ($cart_items as $item) {
                        // Add to order items
                        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, book_id, quantity, price) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$order_id, $item['book_id'], $item['quantity'], $item['price']]);
                        
                        // Update stock
                        $stmt = $pdo->prepare("UPDATE books SET stock_quantity = stock_quantity - ? WHERE id = ?");
                        $stmt->execute([$item['quantity'], $item['book_id']]);
                    }
                    
                    // Clear cart
                    $stmt = $pdo->prepare("DELETE FROM cart WHERE session_id = ?");
                    $stmt->execute([$_SESSION['session_id']]);
                    
                    $pdo->commit();
                    
                    // Debug: Log successful checkout
                    error_log("Checkout successful, redirecting to success page");
                    
                    // Ensure no output before redirect
                    ob_clean();
                    header('Location: cart.php?success=1&order_id=' . $order_id);
                    exit();
                } catch (Exception $e) {
                    $pdo->rollBack();
                    error_log("Checkout error: " . $e->getMessage());
                    $error = "Order processing failed. Please try again.";
                }
            } else {
                $error = "Some items in your cart are no longer available in the requested quantity.";
            }
        } else {
            $error = "Votre panier est vide.";
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}

// Check if we should show success message (don't load cart items if success)
$show_success = isset($_GET['success']) && isset($_GET['order_id']) && !empty($_GET['order_id']);

// Debug: Log success check
if (isset($_GET['success'])) {
    error_log("Success parameter detected: " . $_GET['success']);
    error_log("Order ID parameter: " . (isset($_GET['order_id']) ? $_GET['order_id'] : 'not set'));
}

// Get cart items only if not showing success message
if (!$show_success) {
    $stmt = $pdo->prepare("SELECT c.*, b.title, b.author, b.price, b.image_url, b.stock_quantity 
                           FROM cart c 
                           JOIN books b ON c.book_id = b.id 
                           WHERE c.session_id = ? 
                           ORDER BY c.created_at DESC");
    $stmt->execute([$_SESSION['session_id']]);
    $cart_items = $stmt->fetchAll();

    $total_amount = 0;
    foreach ($cart_items as $item) {
        $total_amount += $item['price'] * $item['quantity'];
    }
} else {
    $cart_items = [];
    $total_amount = 0;
}

// Additional debug for success display
if ($show_success) {
    error_log("Showing success message for order ID: " . $_GET['order_id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - BookStore</title>
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
        .main-content {
            padding: 2rem 0;
        }
        .page-title {
            font-size: 2.5rem;
            margin-bottom: 2rem;
            text-align: center;
            color: #333;
        }
        .alert {
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 5px;
            text-align: center;
        }
        .alert.success {
            background: #4caf50;
            color: white;
        }
        .alert.error {
            background: #f44336;
            color: white;
        }
        .alert.info {
            background: #2196f3;
            color: white;
        }
        .cart-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            margin-top: 2rem;
        }
        .cart-items {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .cart-item {
            display: grid;
            grid-template-columns: 100px 1fr auto auto auto;
            gap: 1rem;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #eee;
        }
        .cart-item:last-child {
            border-bottom: none;
        }
        .item-image {
            width: 80px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
        }
        .item-details h4 {
            margin-bottom: 0.5rem;
            color: #333;
        }
        .item-details p {
            color: #666;
            font-size: 0.9rem;
        }
        .item-price {
            font-weight: bold;
            color: #667eea;
            font-size: 1.1rem;
        }
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .quantity-input {
            width: 60px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-align: center;
        }
        .btn {
            background: #667eea;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
            font-size: 14px;
        }
        .btn:hover {
            background: #5a6fd8;
            transform: translateY(-1px);
        }
        .btn-small {
            padding: 6px 12px;
            font-size: 12px;
        }
        .btn-danger {
            background: #dc3545;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        .btn-warning {
            background: #ffc107;
            color: #333;
        }
        .btn-warning:hover {
            background: #e0a800;
        }
        .checkout-section {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            height: fit-content;
        }
        .order-summary {
            margin-bottom: 2rem;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            padding: 0.5rem 0;
        }
        .summary-total {
            border-top: 2px solid #eee;
            padding-top: 1rem;
            font-size: 1.2rem;
            font-weight: bold;
        }
        .checkout-form {
            margin-top: 2rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        .form-group textarea {
            resize: vertical;
            height: 80px;
        }
        .empty-cart {
            text-align: center;
            padding: 3rem;
            color: #666;
        }
        .empty-cart h3 {
            margin-bottom: 1rem;
        }
        .order-success {
            background: white;
            border-radius: 15px;
            padding: 3rem;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: 2rem auto;
            border: 3px solid #4caf50;
        }
        .order-success h2 {
            color: #4caf50;
            margin-bottom: 1rem;
            font-size: 2rem;
        }
        .order-success .success-icon {
            font-size: 4rem;
            color: #4caf50;
            margin-bottom: 1rem;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        .order-success .order-details {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin: 1.5rem 0;
            border-left: 4px solid #4caf50;
        }
        .order-success .celebration-text {
            color: #4caf50;
            font-size: 1.2rem;
            margin: 1.5rem 0;
            font-weight: bold;
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
        footer a {
            color: #fff;
            text-decoration: underline;
        }
        footer .social-icons {
            margin-top: 1rem;
        }
        footer .social-icons a {
            color: #fff;
            margin-right: 1rem;
            font-size: 1.2rem;
        }
        @media (max-width: 768px) {
            .cart-container {
                grid-template-columns: 1fr;
            }
            .cart-item {
                grid-template-columns: 80px 1fr;
                gap: 1rem;
            }
            .item-price,
            .quantity-controls,
            .item-actions {
                grid-column: 2;
                margin-top: 0.5rem;
            }
        }
        
        /* Debug styles - Remove in production */
        .debug-info {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            font-family: monospace;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <header>
        <nav class="container">
            <div class="logo"><i class="fa-solid fa-book-open"></i> BookStore</div>
            <ul class="nav-links">
                <li><a href="index.php">Accueil</a></li>
                <li><a href="catalog.php">Catalogue</a></li>
                <li><a href="admin.php">Admin</a></li>
            </ul>
            <a href="cart.php" class="cart-icon">
                <i class="fa-solid fa-shopping-cart"></i> Panier
                <span class="cart-count"><?php echo !$show_success ? getCartCount() : 0; ?></span>
            </a>
        </nav>
    </header>

    <div class="container main-content">
        <h1 class="page-title">Mon panier</h1>

        <!-- Debug info - Remove in production -->
        <?php if (isset($_GET['debug'])): ?>
            <div class="debug-info">
                <strong>Debug Info:</strong><br>
                success param: <?php echo isset($_GET['success']) ? $_GET['success'] : 'not set'; ?><br>
                order_id param: <?php echo isset($_GET['order_id']) ? $_GET['order_id'] : 'not set'; ?><br>
                show_success: <?php echo $show_success ? 'true' : 'false'; ?><br>
                POST data: <?php echo !empty($_POST) ? 'present' : 'empty'; ?>
            </div>
        <?php endif; ?>

        <?php if ($show_success): ?>
            <div class="order-success">
                <div class="success-icon">✅</div>
                <h2>Commande passée avec succès !</h2>
                <div class="order-details">
                    <p><strong>Numéro de commande : #<?php echo htmlspecialchars($_GET['order_id']); ?></strong></p>
                    <p>Vous recevrez un email de confirmation sous peu.</p>
                </div>
                <div class="celebration-text">
                    🎉 Félicitations ! Votre achat a bien été enregistré. <br>
                    Nous traitons votre commande et vous contacterons rapidement pour la livraison.
                </div>
                <div style="margin-top: 2rem;">
                    <a href="catalog.php" class="btn" style="margin-right: 1rem;">Continuer mes achats</a>
                    <a href="index.php" class="btn" style="background: #6c757d;">Retour à l'accueil</a>
                </div>
            </div>
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
                            $shipping = $subtotal > 750 ? 0 : 100; // 100 FCFA shipping if under 750 FCFA
                            $tax = $subtotal * 0.08; // 8% tax
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

                        <form method="POST" class="checkout-form">
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
                            
                            <button type="submit" name="checkout" class="btn" style="width: 100%; padding: 15px; font-size: 18px;">
                                🛒 Valider la commande - <?php echo formatPriceFCFA($total); ?>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
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
        // Auto-submit quantity changes
        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', function() {
                if (this.value < 1) this.value = 1;
            });
        });

        // Checkout form validation
        document.querySelector('.checkout-form')?.addEventListener('submit', function(e) {
            const btn = this.querySelector('button[name="checkout"]');
            if (btn) {
                btn.textContent = '⏳ Traitement en cours...';
                btn.disabled = true;
            }
        });

        // Auto-hide alerts after 5 seconds
        document.querySelectorAll('.alert').forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            }, 5000);
        });
    </script>
</body>
</html>