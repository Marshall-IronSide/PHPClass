<?php
session_start();

if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header('Location: admin.php');
    exit;
}

// Vérifier si l'admin est connecté
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: admin.php');
    exit;
}

// Connexion à la base admin_db (pour logs/admins)
include 'config2.php';

// Connexion à la base books_db (pour livres et catégories)
include 'config.php';

// Traitement de l'ajout de livre
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $category_id = $_POST['category'];
    $price = $_POST['price'];
    $description = trim($_POST['description']);
    
    if ($title && $author && $category_id && $price) {
        try {
            // Utilise $pdo de config.php (books_db)
            $stmt = $pdo->prepare("INSERT INTO books (title, author, category_id, price, description) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$title, $author, $category_id, $price, $description]);
            
            // Enregistrement de l'activité (utilise $pdo2 de config2.php si besoin)
            if (isset($_SESSION['admin_id'])) {
                $log_stmt = $pdo2->prepare("INSERT INTO admin_activity_logs (admin_id, action, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
                $log_stmt->execute([
                    $_SESSION['admin_id'],
                    'BOOK_ADDED',
                    'Livre ajouté: ' . $title,
                    $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
                    $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
                ]);
            }
            
            $success_message = "Livre ajouté avec succès !";
        } catch (Exception $e) {
            $error_message = "Erreur lors de l'ajout du livre : " . $e->getMessage();
        }
    } else {
        $error_message = "Veuillez remplir tous les champs obligatoires.";
    }
}

// Récupérer les catégories (depuis books_db)
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

// Récupérer les livres (depuis books_db)
$books = $pdo->query("SELECT books.*, categories.name AS category_name FROM books LEFT JOIN categories ON books.category_id = categories.id ORDER BY books.id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Admin - BookStore</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        html, body {
            height: 100%;
        }
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #8f6ed5 100%);
            font-family: 'Montserrat', Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        
        /* Header avec informations admin */
        .admin-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #2c3e50;
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .admin-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .admin-info i {
            font-size: 1.2rem;
        }
        
        .logout-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s;
        }
        
        .logout-btn:hover {
            background: #c0392b;
        }
        
        .container {
            max-width: 1200px;
            margin: 20px auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(102, 126, 234, 0.10), 0 1.5px 8px rgba(51,71,91,0.08);
            padding: 2.5rem;
        }
        
        h2 {
            text-align: center;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 2.2rem;
            position: relative;
        }
        
        h2::after {
            content: "";
            display: block;
            margin: 18px auto 0 auto;
            width: 80px;
            height: 5px;
            border-radius: 3px;
            background: linear-gradient(90deg, #667eea 0%, #5ec6fa 100%);
            opacity: 0.7;
        }
        
        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        form {
            display: flex;
            flex-wrap: wrap;
            gap: 1.2rem 2%;
            margin-bottom: 2.5rem;
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 12px;
        }
        
        form > * {
            flex: 1 1 48%;
            min-width: 180px;
        }
        
        form textarea {
            flex: 1 1 100%;
            min-height: 80px;
        }
        
        label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: block;
            color: #333;
        }
        
        input, select, textarea {
            width: 100%;
            padding: 0.8rem;
            border-radius: 8px;
            border: 2px solid #e1e5ee;
            font-size: 1rem;
            margin-bottom: 0.7rem;
            background: #fff;
            transition: border-color 0.3s;
        }
        
        input:focus, select:focus, textarea:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
        }
        
        button, .btn {
            background: #667eea;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 0.8rem 1.5rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        button:hover, .btn:hover {
            background: #5a6fd8;
            transform: translateY(-2px);
        }
        
        .btn-danger {
            background: #e74c3c;
        }
        
        .btn-danger:hover {
            background: #c0392b;
        }
        
        .btn-secondary {
            background: #6c757d;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .table-container {
            margin-top: 2.5rem;
        }
        
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.08);
        }
        
        th, td {
            padding: 1rem 0.8rem;
            text-align: left;
        }
        
        th {
            background: #f8f9fa;
            font-weight: 700;
            color: #2c3e50;
        }
        
        tr:not(:last-child) {
            border-bottom: 1px solid #e3e8f0;
        }
        
        tbody tr:hover {
            background: #f8f9fa;
        }
        
        .actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .actions .btn {
            padding: 0.4rem 0.8rem;
            font-size: 0.9rem;
        }
        
        .price {
            font-weight: 600;
            color: #27ae60;
        }
        
        .navigation-links {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        
        .nav-btn {
            background: #34495e;
            color: white;
            padding: 0.6rem 1.2rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.3s;
        }
        
        .nav-btn:hover {
            background: #2c3e50;
        }
        
        @media (max-width: 800px) {
            .container { 
                padding: 1.5rem; 
                margin: 20px 10px;
            }
            
            .admin-header {
                padding: 1rem;
                flex-direction: column;
                gap: 1rem;
                height: auto;
            }
            
            body {
                padding-top: 120px;
            }
            
            form { 
                flex-direction: column;
                padding: 1.5rem;
            }
            
            form > * { 
                flex: 1 1 100%; 
            }
            
            .table-container {
                overflow-x: auto;
            }
            
            .actions {
                flex-direction: column;
            }
            
            .navigation-links {
                flex-direction: column;
            }
        }
        
        .admin-header-main {
            text-align: center;
            padding-top: 2.5rem;
            padding-bottom: 1.5rem;
        }
        .admin-header-main .admin-title {
            font-size: 2.2rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.7rem;
        }
        .admin-header-main .admin-title i {
            font-size: 2.2rem;
        }
        .footer-admin {
            background: rgba(44,62,80,0.97);
            color: #fff;
            text-align: center;
            padding: 1.5rem 0 0.7rem 0;
            margin-top: 3rem;
            font-size: 1rem;
        }
        .footer-admin a {
            color: #b3c6ff;
            text-decoration: underline;
            margin: 0 0.5rem;
        }
    </style>
</head>
<body>
    <!-- En-tête principal -->
    <div class="admin-header-main">
        <div class="admin-title">
            <i class="fas fa-user-shield"></i>
            BookStore Admin
        </div>
    </div>
    <!-- Header Admin connecté -->
    <div class="admin-header">
        <div class="admin-info">
            <i class="fas fa-user-shield"></i>
            <span>Bienvenue, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
            <span>|</span>
            <span><?php echo htmlspecialchars($_SESSION['admin_email']); ?></span>
        </div>
        <form method="POST" action="tdb.php" style="margin: 0;">
            <button type="submit" name="logout" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Déconnexion
            </button>
        </form>
    </div>

    <div class="container">
        <h2>Tableau de bord Administration</h2>
        
        <!-- Navigation -->
        <div class="navigation-links">
            <a href="index.php" class="nav-btn">
                <i class="fas fa-home"></i> Voir le site
            </a>
            <a href="#" class="nav-btn">
                <i class="fas fa-users"></i> Gestion utilisateurs
            </a>
            <a href="#" class="nav-btn">
                <i class="fas fa-chart-line"></i> Statistiques
            </a>
        </div>
        
        <!-- Messages -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-triangle"></i>
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <!-- Formulaire d'ajout de livre -->
        <form method="POST" action="">
            <div>
                <label for="title"><i class="fas fa-book"></i> Titre du livre *</label>
                <input type="text" id="title" name="title" placeholder="Entrez le titre du livre" required>
            </div>
            <div>
                <label for="author"><i class="fas fa-user-edit"></i> Auteur *</label>
                <input type="text" id="author" name="author" placeholder="Nom de l'auteur" required>
            </div>
            <div>
                <label for="category"><i class="fas fa-tags"></i> Catégorie *</label>
                <select id="category" name="category" required>
                    <option value="">Sélectionnez une catégorie</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="price"><i class="fas fa-coins"></i> Prix (en FCFA) *</label>
                <input type="number" id="price" name="price" step="1" min="0" placeholder="0" required>
            </div>
            <div style="flex: 1 1 100%;">
                <label for="description"><i class="fas fa-align-left"></i> Description</label>
                <textarea id="description" name="description" placeholder="Description du livre (optionnel)"></textarea>
            </div>
            <div style="flex: 1 1 100%;">
                <button type="submit">
                    <i class="fas fa-plus"></i> Ajouter le livre
                </button>
            </div>
        </form>

        <!-- Tableau des livres -->
        <div class="table-container">
            <div class="table-header">
                <h3><i class="fas fa-list"></i> Inventaire des livres (<?php echo count($books); ?> livres)</h3>
            </div>
            
            <?php if (empty($books)): ?>
                <div style="text-align: center; padding: 2rem; color: #6c757d;">
                    <i class="fas fa-book-open" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                    <p>Aucun livre dans l'inventaire. Ajoutez votre premier livre !</p>
                </div>
            <?php else: ?>
                <table>
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
                        <?php foreach ($books as $book): ?>
                            <tr>
                                <td>#<?php echo $book['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($book['title']); ?></strong>
                                    <?php if ($book['description']): ?>
                                        <br><small style="color: #6c757d;"><?php echo htmlspecialchars(substr($book['description'], 0, 50)) . (strlen($book['description']) > 50 ? '...' : ''); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($book['author']); ?></td>
                                <td>
                                    <span style="background: #e3f2fd; color: #1976d2; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.9rem;">
                                        <?php echo htmlspecialchars($book['category_name'] ?? 'Non catégorisé'); ?>
                                    </span>
                                </td>
                                <td class="price">
                                    <?php echo number_format($book['price'], 0, ',', ' ') . ' FCFA'; ?>
                                </td>
                                <td class="actions">
                                    <a href="edit.php?id=<?php echo $book['id']; ?>" class="btn btn-secondary">
                                        <i class="fas fa-edit"></i> Modifier
                                    </a>
                                    <a href="delete.php?id=<?php echo $book['id']; ?>" class="btn btn-danger" 
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer le livre \'<?php echo htmlspecialchars($book['title']); ?>\' ?')">
                                        <i class="fas fa-trash"></i> Supprimer
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <footer class="footer-admin">
        &copy; 2025 BookStore Administration. Tous droits réservés. |
        <a href="#">Politique de confidentialité</a> |
        <a href="#">Conditions d'utilisation</a>
    </footer>

    <script>
        // Auto-focus sur le premier champ du formulaire
        document.addEventListener('DOMContentLoaded', function() {
            const firstInput = document.querySelector('#title');
            if (firstInput) {
                firstInput.focus();
            }
        });
        
        // Confirmation avant suppression
        document.querySelectorAll('.btn-danger[href*="delete.php"]').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                if (!confirm('Cette action est irréversible. Voulez-vous vraiment supprimer ce livre ?')) {
                    e.preventDefault();
                }
            });
        });
        
        // Masquer les alertes après 5 secondes
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.5s';
                setTimeout(function() {
                    alert.remove();
                }, 500);
            });
        }, 5000);
    </script>
</body>
</html>