<?php
// Connexion à la base admin_db (pour logs/admins)
include_once 'config2.php';
// Connexion à la base books_db (pour livres et catégories)
include_once 'config.php';

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

// Variable pour stocker le livre à éditer
$book_to_edit = null;

// Traitement de l'ajout de livre
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title']) && !isset($_POST['edit_book'])) {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $category_id = $_POST['category'];
    // CORRECTION: Diviser le prix saisi par 655 pour le stocker en unité de base
    $price = $_POST['price'] / 655;
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

// Suppression
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Enregistrer l'activité de suppression avant de supprimer
    if (isset($_SESSION['admin_id'])) {
        // Récupérer le titre du livre avant suppression
        $stmt_title = $pdo->prepare("SELECT title FROM books WHERE id = ?");
        $stmt_title->execute([$id]);
        $book_title = $stmt_title->fetchColumn();
        
        if ($book_title) {
            $log_stmt = $pdo2->prepare("INSERT INTO admin_activity_logs (admin_id, action, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
            $log_stmt->execute([
                $_SESSION['admin_id'],
                'BOOK_DELETED',
                'Livre supprimé: ' . $book_title,
                $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
            ]);
        }
    }
    
    $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: tdb.php?deleted=1');
    exit;
}

// Traitement de la modification
if (isset($_POST['edit_book'])) {
    $id = (int)$_POST['id'];
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $category_id = $_POST['category'];
    $price = $_POST['price'] / 655;
    $description = trim($_POST['description']);
    
    if ($title && $author && $category_id && $price) {
        try {
            $stmt = $pdo->prepare("UPDATE books SET title = ?, author = ?, category_id = ?, price = ?, description = ? WHERE id = ?");
            $stmt->execute([$title, $author, $category_id, $price, $description, $id]);
            
            // Enregistrer l'activité de modification
            if (isset($_SESSION['admin_id'])) {
                $log_stmt = $pdo2->prepare("INSERT INTO admin_activity_logs (admin_id, action, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
                $log_stmt->execute([
                    $_SESSION['admin_id'],
                    'BOOK_UPDATED',
                    'Livre modifié: ' . $title,
                    $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
                    $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
                ]);
            }
            
            header('Location: tdb.php?updated=1');
            exit;
        } catch (Exception $e) {
            $error_message = "Erreur lors de la modification : " . $e->getMessage();
        }
    } else {
        $error_message = "Veuillez remplir tous les champs obligatoires.";
    }
}

// Récupération du livre à éditer (APRÈS le traitement de modification)
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$id]);
    $book_to_edit = $stmt->fetch();
    
    if (!$book_to_edit) {
        $error_message = "Livre introuvable.";
    }
}

// Messages de confirmation
if (isset($_GET['deleted'])) {
    $success_message = "Livre supprimé avec succès !";
}
if (isset($_GET['updated'])) {
    $success_message = "Livre modifié avec succès !";
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
    <link rel="stylesheet" href="styles3.css">
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
                
                <!-- Formulaire d'édition de livre (affiché en premier si en mode édition) -->
                <?php if ($book_to_edit): ?>
                    <div class="form-section" id="edit-book-form">
                        <div class="section-title">
                            <i class="fas fa-edit"></i>
                            Éditer le livre : <?php echo htmlspecialchars($book_to_edit['title']); ?>
                        </div>
                        
                        <form method="POST">
                            <input type="hidden" name="id" value="<?php echo $book_to_edit['id']; ?>">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="edit_title">Titre du livre *</label>
                                    <div class="input-icon">
                                        <i class="fas fa-book"></i>
                                        <input type="text" id="edit_title" name="title" value="<?php echo htmlspecialchars($book_to_edit['title']); ?>" required>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="edit_author">Auteur *</label>
                                    <div class="input-icon">
                                        <i class="fas fa-user-edit"></i>
                                        <input type="text" id="edit_author" name="author" value="<?php echo htmlspecialchars($book_to_edit['author']); ?>" required>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="edit_category">Catégorie *</label>
                                    <div class="input-icon">
                                        <i class="fas fa-tags"></i>
                                        <select id="edit_category" name="category" required>
                                            <option value="">Sélectionnez une catégorie</option>
                                            <?php foreach ($categories as $cat): ?>
                                                <option value="<?php echo $cat['id']; ?>" <?php echo $cat['id'] == $book_to_edit['category_id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="edit_price">Prix (en FCFA) *</label>
                                    <div class="input-icon">
                                        <i class="fas fa-coins"></i>
                                        <input type="number" id="edit_price" name="price" step="1" min="0" value="<?php echo round($book_to_edit['price'] * 655); ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="edit_description">Description</label>
                                <div class="input-icon">
                                    <i class="fas fa-align-left"></i>
                                    <textarea id="edit_description" name="description" placeholder="Description du livre (optionnel)"><?php echo htmlspecialchars($book_to_edit['description']); ?></textarea>
                                </div>
                            </div>
                            
                            <div style="display: flex; gap: 10px;">
                                <button type="submit" name="edit_book" class="btn">
                                    <i class="fas fa-save"></i> Enregistrer les modifications
                                </button>
                                <a href="tdb.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Annuler
                                </a>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
                
                <!-- Formulaire d'ajout de livre (masqué si en mode édition) -->
                <?php if (!$book_to_edit): ?>
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
                <?php endif; ?>

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
                                        <tr <?php echo ($book_to_edit && $book['id'] == $book_to_edit['id']) ? 'style="background-color: #e3f2fd;"' : ''; ?>>
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
                                                <a href="tdb.php?action=edit&id=<?php echo $book['id']; ?>" 
                                                   class="btn btn-secondary <?php echo ($book_to_edit && $book['id'] == $book_to_edit['id']) ? 'active' : ''; ?>">
                                                    <i class="fas fa-edit"></i> <?php echo ($book_to_edit && $book['id'] == $book_to_edit['id']) ? 'En cours...' : 'Modifier'; ?>
                                                </a>
                                                <a href="tdb.php?action=delete&id=<?php echo $book['id']; ?>" class="btn btn-danger" 
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
            const firstInput = document.querySelector('#title, #edit_title');
            if (firstInput) {
                firstInput.focus();
            }
        });
        
        // Confirmation avant suppression
        document.querySelectorAll('.btn-danger[href*="delete"]').forEach(function(btn) {
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