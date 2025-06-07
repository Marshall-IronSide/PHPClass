<?php
// Connexion à la base admin_db (pour logs/admins)
include 'config2.php';

// Connexion à la base books_db (pour livres et catégories)
include 'config.php';
//session_start();

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
    <title>BookStore | Tableau de bord</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
        }
        
        .logo {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            color: white;
        }
        
        .logo i {
            font-size: 2.5rem;
            margin-right: 15px;
        }
        
        .logo h1 {
            font-size: 2.8rem;
            font-weight: bold;
            text-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .admin-header {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 15px;
            color: white;
            margin-bottom: 20px;
            text-align: center;
            width: 100%;
            max-width: 1200px;
            backdrop-filter: blur(10px);
        }
        
        .admin-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .admin-details {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .admin-details i {
            font-size: 1.2rem;
        }
        
        .logout-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .logout-btn:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }
        
        .main-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.25);
            width: 100%;
            max-width: 1200px;
            overflow: hidden;
            margin-bottom: 20px;
        }
        
        .card-header {
            background: #2c3e50;
            color: white;
            padding: 25px;
            text-align: center;
        }
        
        .card-header h2 {
            font-size: 1.8rem;
            margin-bottom: 5px;
        }
        
        .card-body {
            padding: 30px;
        }
        
        .navigation-links {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        .nav-btn {
            background: #667eea;
            color: white;
            padding: 12px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .nav-btn:hover {
            background: #5a6fd8;
            transform: translateY(-2px);
        }
        
        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 25px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
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
        
        .form-section {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        .input-icon {
            position: relative;
        }
        
        .input-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #667eea;
        }
        
        .input-icon input,
        .input-icon select,
        .input-icon textarea {
            width: 100%;
            padding: 14px 14px 14px 45px;
            border: 2px solid #e1e5ee;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .input-icon textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .input-icon input:focus,
        .input-icon select:focus,
        .input-icon textarea:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
        }
        
        .btn {
            display: inline-block;
            padding: 14px 20px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            text-align: center;
        }
        
        .btn:hover {
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
        
        .table-section {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .table-header {
            background: #2c3e50;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .table-title {
            font-size: 1.3rem;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .table-container {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e1e5ee;
        }
        
        th {
            background: #f8f9fa;
            font-weight: 700;
            color: #2c3e50;
        }
        
        tbody tr:hover {
            background: #f8f9fa;
        }
        
        .price {
            font-weight: 600;
            color: #27ae60;
        }
        
        .category-badge {
            background: #e3f2fd;
            color: #1976d2;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.9rem;
            display: inline-block;
        }
        
        .actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .actions .btn {
            padding: 8px 12px;
            font-size: 0.9rem;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .footer {
            margin-top: 40px;
            color: white;
            text-align: center;
            font-size: 1rem;
        }
        
        .footer a {
            color: white;
            text-decoration: none;
            font-weight: 600;
        }
        
        .footer a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            .logo h1 {
                font-size: 2rem;
            }
            
            .logo i {
                font-size: 2rem;
            }
            
            .admin-info {
                flex-direction: column;
                text-align: center;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .navigation-links {
                flex-direction: column;
            }
            
            .table-header {
                flex-direction: column;
                gap: 15px;
            }
            
            .actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <i class="fas fa-user-shield"></i>
            <h1>BookStore Admin</h1>
        </div>
        
        <!-- Header Admin connecté -->
        <div class="admin-header">
            <div class="admin-info">
                <div class="admin-details">
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
        </div>

        <div class="main-card">
            <div class="card-header">
                <h2>Tableau de bord Administration</h2>
                <p>Gestion complète de votre librairie BookStore</p>
            </div>
            <div class="card-body">
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
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-plus-circle"></i>
                        Ajouter un nouveau livre
                    </div>
                    
                    <form method="POST" action="">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="title">Titre du livre *</label>
                                <div class="input-icon">
                                    <i class="fas fa-book"></i>
                                    <input type="text" id="title" name="title" placeholder="Entrez le titre du livre" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="author">Auteur *</label>
                                <div class="input-icon">
                                    <i class="fas fa-user-edit"></i>
                                    <input type="text" id="author" name="author" placeholder="Nom de l'auteur" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="category">Catégorie *</label>
                                <div class="input-icon">
                                    <i class="fas fa-tags"></i>
                                    <select id="category" name="category" required>
                                        <option value="">Sélectionnez une catégorie</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="price">Prix (en FCFA) *</label>
                                <div class="input-icon">
                                    <i class="fas fa-coins"></i>
                                    <input type="number" id="price" name="price" step="1" min="0" placeholder="0" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Description</label>
                            <div class="input-icon">
                                <i class="fas fa-align-left"></i>
                                <textarea id="description" name="description" placeholder="Description du livre (optionnel)"></textarea>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn">
                            <i class="fas fa-plus"></i> Ajouter le livre
                        </button>
                    </form>
                </div>

                <!-- Tableau des livres -->
                <div class="table-section">
                    <div class="table-header">
                        <div class="table-title">
                            <i class="fas fa-list"></i>
                            Inventaire des livres (<?php echo count($books); ?> livres)
                        </div>
                    </div>
                    
                    <?php if (empty($books)): ?>
                        <div class="empty-state">
                            <i class="fas fa-book-open"></i>
                            <p>Aucun livre dans l'inventaire. Ajoutez votre premier livre !</p>
                        </div>
                    <?php else: ?>
                        <div class="table-container">
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
                                                <span class="category-badge">
                                                    <?php echo htmlspecialchars($book['category_name'] ?? 'Non catégorisé'); ?>
                                                </span>
                                            </td>
                                            <td class="price">
                                                <?php echo number_format($book['price'] * 655, 0, ',', ' ') . ' FCFA'; ?>
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
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="footer">
            <p>&copy; 2023 BookStore Administration. Tous droits réservés. | 
                <a href="#">Politique de confidentialité</a> | 
                <a href="#">Conditions d'utilisation</a>
            </p>
        </div>
    </div>

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