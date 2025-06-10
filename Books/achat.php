<?php
include 'config.php';

// Vérifier si nous avons un ID de commande valide
if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    header('Location: cart.php');
    exit;
}

$order_id = (int)$_GET['order_id'];

// Récupérer les détails de la commande
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: cart.php');
    exit;
}

// Récupérer les articles de la commande
$stmt = $pdo->prepare("SELECT oi.*, b.title, b.author, b.image_url 
                       FROM order_items oi 
                       JOIN books b ON oi.book_id = b.id 
                       WHERE oi.order_id = ?");
$stmt->execute([$order_id]);
$order_items = $stmt->fetchAll();

// Calculer les détails de facturation
$subtotal = 0;
$item_count = 0;
foreach ($order_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
    $item_count += $item['quantity'];
}
$shipping = $subtotal > 750 ? 0 : 100;
$tax = $subtotal * 0.08;
$total = $subtotal + $shipping + $tax;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation d'achat - BookStore</title>
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- CSS externe -->
    <link rel="stylesheet" href="styles2.css">
    <style>
        .confirmation-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
        }
        
        .success-header {
            text-align: center;
            padding: 3rem 2rem;
            background: linear-gradient(135deg, #e8f5e8, #f0f8f0);
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .success-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }
        
        .order-summary-card {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .order-item {
            display: flex;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #eee;
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .item-image {
            width: 60px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
            margin-right: 1rem;
        }
        
        .item-info {
            flex: 1;
        }
        
        .item-price {
            font-weight: bold;
            color: #2c5282;
        }
        
        .billing-summary {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin-top: 1rem;
        }
        
        .billing-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
        
        .billing-total {
            font-weight: bold;
            font-size: 1.2rem;
            color: #2c5282;
            border-top: 2px solid #2c5282;
            padding-top: 0.5rem;
            margin-top: 1rem;
        }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }
        
        .customer-info {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        
        @media (max-width: 768px) {
            .confirmation-container {
                margin: 1rem;
                padding: 1rem;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .order-item {
                flex-direction: column;
                text-align: center;
            }
            
            .item-image {
                margin: 0 0 1rem 0;
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
                <li><a href="catalog.php">Catalogue</a></li>
                <li><a href="admin.php">Admin</a></li>
            </ul>
            <a href="cart.php" class="cart-icon">
                <i class="fa-solid fa-shopping-cart"></i> Panier
                <span class="cart-count"><?php echo getCartCount(); ?></span>
            </a>
        </nav>
    </header>

    <div class="container">
        <div class="confirmation-container">
            <!-- En-tête de succès -->
            <div class="success-header">
                <div class="success-icon">🎉</div>
                <h1 style="color: #28a745; margin-bottom: 1rem; font-size: 2.5rem;">Commande confirmée !</h1>
                <p style="font-size: 1.2rem; color: #333; margin-bottom: 1.5rem;">
                    Merci pour votre achat ! Votre commande a été traitée avec succès.
                </p>
                <div style="background: white; padding: 1rem; border-radius: 8px; display: inline-block;">
                    <strong style="font-size: 1.3rem; color: #2c5282;">
                        Commande #<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?>
                    </strong>
                </div>
            </div>

            <!-- Informations client -->
            <div class="customer-info">
                <h3 style="margin-bottom: 1rem; color: #2c5282;">
                    <i class="fa-solid fa-user"></i> Informations de livraison
                </h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
                    <div>
                        <strong>Nom :</strong> <?php echo htmlspecialchars($order['customer_name']); ?>
                    </div>
                    <div>
                        <strong>Email :</strong> <?php echo htmlspecialchars($order['customer_email']); ?>
                    </div>
                    <?php if ($order['customer_phone']): ?>
                    <div>
                        <strong>Téléphone :</strong> <?php echo htmlspecialchars($order['customer_phone']); ?>
                    </div>
                    <?php endif; ?>
                </div>
                <div style="margin-top: 1rem;">
                    <strong>Adresse de livraison :</strong><br>
                    <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?>
                </div>
            </div>

            <!-- Résumé de la commande -->
            <div class="order-summary-card">
                <h3 style="margin-bottom: 1.5rem; color: #2c5282;">
                    <i class="fa-solid fa-box"></i> Détails de votre commande
                </h3>
                
                <?php foreach ($order_items as $item): ?>
                <div class="order-item">
                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($item['title']); ?>" 
                         class="item-image">
                    <div class="item-info">
                        <h4 style="margin: 0 0 0.5rem 0;"><?php echo htmlspecialchars($item['title']); ?></h4>
                        <p style="margin: 0; color: #666;">par <?php echo htmlspecialchars($item['author']); ?></p>
                        <p style="margin: 0.5rem 0 0 0; color: #888;">Quantité : <?php echo $item['quantity']; ?></p>
                    </div>
                    <div class="item-price">
                        <?php echo formatPriceFCFA($item['price'] * $item['quantity']); ?>
                    </div>
                </div>
                <?php endforeach; ?>

                <!-- Résumé de facturation -->
                <div class="billing-summary">
                    <h4 style="margin-bottom: 1rem; color: #2c5282;">Résumé de facturation</h4>
                    
                    <div class="billing-row">
                        <span>Sous-total (<?php echo $item_count; ?> articles) :</span>
                        <span><?php echo formatPriceFCFA($subtotal); ?></span>
                    </div>
                    
                    <div class="billing-row">
                        <span>Frais de livraison :</span>
                        <span><?php echo $shipping > 0 ? formatPriceFCFA($shipping) : 'GRATUIT'; ?></span>
                    </div>
                    
                    <div class="billing-row">
                        <span>Taxe (8%) :</span>
                        <span><?php echo formatPriceFCFA($tax); ?></span>
                    </div>
                    
                    <div class="billing-row billing-total">
                        <span>Total payé :</span>
                        <span><?php echo formatPriceFCFA($total); ?></span>
                    </div>
                </div>
            </div>

            <!-- Informations complémentaires -->
            <div class="order-summary-card">
                <h3 style="color: #2c5282; margin-bottom: 1rem;">
                    <i class="fa-solid fa-info-circle"></i> Que se passe-t-il maintenant ?
                </h3>
                <div style="color: #666; line-height: 1.6;">
                    <p><strong>📧 Confirmation par email :</strong> Vous recevrez un email de confirmation à l'adresse <?php echo htmlspecialchars($order['customer_email']); ?></p>
                    <p><strong>📦 Préparation :</strong> Votre commande sera préparée dans les 24 heures</p>
                    <p><strong>🚚 Livraison :</strong> Nous vous contacterons pour organiser la livraison</p>
                    <p><strong>💬 Support :</strong> Pour toute question, contactez-nous à marshallironside7@gmail.com</p>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="action-buttons">
                <a href="catalog.php" class="btn" style="background: #28a745; padding: 15px 30px; text-decoration: none; color: white; border-radius: 8px; display: inline-block; font-weight: bold;">
                    <i class="fa-solid fa-shopping-bag"></i> Continuer mes achats
                </a>
                <a href="index.php" class="btn" style="background: #6c757d; padding: 15px 30px; text-decoration: none; color: white; border-radius: 8px; display: inline-block; font-weight: bold;">
                    <i class="fa-solid fa-home"></i> Retour à l'accueil
                </a>
                <button onclick="window.print()" class="btn" style="background: #17a2b8; padding: 15px 30px; border: none; color: white; border-radius: 8px; cursor: pointer; font-weight: bold;">
                    <i class="fa-solid fa-print"></i> Imprimer
                </button>
            </div>
        </div>
    </div>

    <!-- Footer -->
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
        // Animation d'entrée
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.order-summary-card, .customer-info');
            elements.forEach((el, index) => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    el.style.transition = 'all 0.6s ease';
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                }, index * 200);
            });
        });

        // Empêcher le retour en arrière accidentel
        window.history.pushState(null, "", window.location.href);
        window.onpopstate = function() {
            window.history.pushState(null, "", window.location.href);
        };
    </script>
</body>
</html>