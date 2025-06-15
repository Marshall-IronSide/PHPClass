<?php
session_start();

// Configuration de la base de données
$host = 'localhost';
$port = '3377'; 
$dbname = 'admin_db';
$username = 'root';
$password = ''; 

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Vérifier si l'admin est déjà connecté
$admin_logged_in = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'];

// Si déjà connecté, rediriger vers le tableau de bord
if ($admin_logged_in) {
    header('Location: tdb.php');
    exit;
}

// Traitement de la connexion
if (isset($_POST['admin_login'])) {
    $email = trim($_POST['admin_username']);
    $password = $_POST['admin_password'];
    
    if ($email && $password) {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ? AND status = 'active'");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['password'])) {
            // Mise à jour de la dernière connexion
            $update_stmt = $pdo->prepare("UPDATE admins SET last_login = NOW() WHERE id = ?");
            $update_stmt->execute([$admin['id']]);
            
            // Enregistrement de l'activité de connexion
            $log_stmt = $pdo->prepare("INSERT INTO admin_activity_logs (admin_id, action, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
            $log_stmt->execute([
                $admin['id'],
                'LOGIN',
                'Connexion réussie',
                $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
            ]);
            
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['full_name'];
            $_SESSION['admin_email'] = $admin['email'];
            header('Location: tdb.php');
            exit;
        } else {
            $login_error = "Email ou mot de passe incorrect, ou compte inactif";
            
            // Enregistrement de la tentative de connexion échouée
            if ($admin) {
                $log_stmt = $pdo->prepare("INSERT INTO admin_activity_logs (admin_id, action, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
                $log_stmt->execute([
                    $admin['id'],
                    'LOGIN_FAILED',
                    'Tentative de connexion avec mot de passe incorrect',
                    $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
                    $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
                ]);
            }
        }
    } else {
        $login_error = "Veuillez remplir tous les champs";
    }
}

// Traitement de la création de compte
if (isset($_POST['create_account'])) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($full_name && $email && $password && $confirm_password) {
        if ($password === $confirm_password) {
            if (strlen($password) >= 8) {
                // Validation du format de l'email
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    // Vérifier si l'email existe déjà
                    $stmt = $pdo->prepare("SELECT id FROM admins WHERE email = ?");
                    $stmt->execute([$email]);
                    
                    if (!$stmt->fetch()) {
                        // Hasher le mot de passe
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        
                        // Insérer le nouvel admin
                        $stmt = $pdo->prepare("INSERT INTO admins (full_name, email, password, status) VALUES (?, ?, ?, 'active')");
                        if ($stmt->execute([$full_name, $email, $hashed_password])) {
                            $new_admin_id = $pdo->lastInsertId();
                            
                            // Enregistrement de l'activité de création de compte
                            $log_stmt = $pdo->prepare("INSERT INTO admin_activity_logs (admin_id, action, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
                            $log_stmt->execute([
                                $new_admin_id,
                                'ACCOUNT_CREATED',
                                'Nouveau compte administrateur créé',
                                $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
                                $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
                            ]);
                            
                            $success_message = "Compte créé avec succès ! Vous pouvez maintenant vous connecter.";
                            $show_login = true;
                        } else {
                            $create_error = "Erreur lors de la création du compte";
                        }
                    } else {
                        $create_error = "Un compte avec cette adresse email existe déjà";
                    }
                } else {
                    $create_error = "Format d'email invalide";
                }
            } else {
                $create_error = "Le mot de passe doit contenir au moins 8 caractères";
            }
        } else {
            $create_error = "Les mots de passe ne correspondent pas";
        }
    } else {
        $create_error = "Veuillez remplir tous les champs";
    }
}

// Traitement de la déconnexion
if (isset($_POST['logout'])) {
    if (isset($_SESSION['admin_id'])) {
        // Enregistrement de l'activité de déconnexion
        $log_stmt = $pdo->prepare("INSERT INTO admin_activity_logs (admin_id, action, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
        $log_stmt->execute([
            $_SESSION['admin_id'],
            'LOGOUT',
            'Déconnexion',
            $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
            $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        ]);
    }
    
    unset($_SESSION['admin_logged_in']);
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_name']);
    unset($_SESSION['admin_email']);
    $admin_logged_in = false;
}

// Fonction pour obtenir les statistiques des admins (optionnel)
function getAdminStats($pdo) {
    try {
        $stmt = $pdo->query("SELECT 
            COUNT(*) as total_admins,
            SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_admins,
            SUM(CASE WHEN last_login >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as recent_logins
            FROM admins");
        return $stmt->fetch();
    } catch (Exception $e) {
        return null;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookStore | Administration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body class="admin">
    <div class="admin-container">
        <div class="admin-logo">
            <i class="fas fa-user-shield"></i>
            <h1>BookStore Admin</h1>
        </div>
        
        <?php 
        // Affichage des statistiques (optionnel)
        $stats = getAdminStats($pdo);
        if ($stats): 
        ?>
        <div class="admin-stats-info">
            <h4>Statistiques de l'administration</h4>
            <div class="admin-stats-grid">
                <div class="admin-stat-item">
                    <span class="admin-stat-number"><?php echo $stats['total_admins']; ?></span>
                    <span class="admin-stat-label">Total Admins</span>
                </div>
                <div class="admin-stat-item">
                    <span class="admin-stat-number"><?php echo $stats['active_admins']; ?></span>
                    <span class="admin-stat-label">Actifs</span>
                </div>
                <div class="admin-stat-item">
                    <span class="admin-stat-number"><?php echo $stats['recent_logins']; ?></span>
                    <span class="admin-stat-label">Connexions récentes</span>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="admin-card-container">
            <!-- Login Card -->
            <div class="admin-card" id="loginCard">
                <div class="admin-card-header">
                    <h2>Connexion Administration</h2>
                    <p>Accédez au panneau d'administration BookStore</p>
                </div>
                <div class="admin-card-body">
                    <?php if (isset($login_error)): ?>
                        <div class="admin-alert admin-alert-error">
                            <i class="fas fa-exclamation-triangle"></i>
                            <?php echo $login_error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($success_message)): ?>
                        <div class="admin-alert admin-alert-success">
                            <i class="fas fa-check-circle"></i>
                            <?php echo $success_message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form id="loginForm" method="POST" action="">
                        <div class="admin-form-group">
                            <label for="loginEmail">Adresse e-mail</label>
                            <div class="admin-input-icon">
                                <i class="fas fa-envelope"></i>
                                <input type="email" id="loginEmail" name="admin_username" placeholder="admin@bookstore.com" required>
                            </div>
                        </div>
                        
                        <div class="admin-form-group">
                            <label for="loginPassword">Mot de passe</label>
                            <div class="admin-input-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password" id="loginPassword" name="admin_password" placeholder="Entrez votre mot de passe" required>
                            </div>
                        </div>
                        
                        <div class="admin-form-group">
                            <button type="submit" class="admin-btn" name="admin_login">
                                <i class="fas fa-sign-in-alt"></i> Se connecter
                            </button>
                        </div>
                        
                        <div class="admin-links">
                            <a href="#" id="forgotPassword">Mot de passe oublié ?</a>
                            <a href="#" id="createAccountLink">Créer un compte admin</a>
                        </div>
                    </form>
                    
                    <div class="admin-form-group" style="margin-top: 30px;">
                        <a href="index.php" class="admin-btn admin-btn-tertiary">
                            <i class="fas fa-home"></i> Retour à l'accueil
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Create Account Card (Hidden by default) -->
            <div class="admin-card" id="createAccountCard" style="display: none;">
                <div class="admin-card-header">
                    <h2>Créer un compte admin</h2>
                    <p>Nouveau compte d'administration</p>
                </div>
                <div class="admin-card-body">
                    <?php if (isset($create_error)): ?>
                        <div class="admin-alert admin-alert-error">
                            <i class="fas fa-exclamation-triangle"></i>
                            <?php echo $create_error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form id="createAccountForm" method="POST" action="">
                        <div class="admin-form-group">
                            <label for="fullName">Nom complet</label>
                            <div class="admin-input-icon">
                                <i class="fas fa-user"></i>
                                <input type="text" id="fullName" name="full_name" placeholder="Entrez votre nom complet" required>
                            </div>
                        </div>
                        
                        <div class="admin-form-group">
                            <label for="email">Adresse e-mail</label>
                            <div class="admin-input-icon">
                                <i class="fas fa-envelope"></i>
                                <input type="email" id="email" name="email" placeholder="Entrez votre e-mail" required>
                            </div>
                        </div>
                        
                        <div class="admin-form-group">
                            <label for="password">Mot de passe</label>
                            <div class="admin-input-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password" id="password" name="password" placeholder="Créez un mot de passe" required>
                            </div>
                            <div class="admin-password-strength">
                                <div class="admin-strength-meter" id="passwordStrength"></div>
                            </div>
                        </div>
                        
                        <div class="admin-form-group">
                            <label for="confirmPassword">Confirmez le mot de passe</label>
                            <div class="admin-input-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password" id="confirmPassword" name="confirm_password" placeholder="Confirmez votre mot de passe" required>
                            </div>
                        </div>
                        
                        <div class="admin-form-group">
                            <button type="submit" class="admin-btn" name="create_account">
                                <i class="fas fa-user-plus"></i> Créer le compte
                            </button>
                        </div>
                    </form>
                    
                    <div class="admin-links">
                        <a href="#" id="backToLogin">Retour à la connexion</a>
                    </div>
                    
                    <div class="admin-form-group" style="margin-top: 20px;">
                        <a href="index.php" class="admin-btn admin-btn-tertiary">
                            <i class="fas fa-home"></i> Retour à l'accueil
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Admin Benefits -->
            <div class="admin-create-account-info" id="benefitsInfo">
                <h3>Administration BookStore</h3>
                <ul class="admin-benefits">
                    <li><i class="fas fa-cogs"></i> Gestion complète du catalogue de livres</li>
                    <li><i class="fas fa-users"></i> Administration des comptes utilisateurs</li>
                    <li><i class="fas fa-chart-line"></i> Tableaux de bord et statistiques</li>
                    <li><i class="fas fa-shopping-cart"></i> Suivi des commandes et ventes</li>
                    <li><i class="fas fa-shield-alt"></i> Sécurité et contrôle d'accès</li>
                </ul>
                <p>Interface d'administration sécurisée pour gérer efficacement votre librairie en ligne BookStore.</p>
            </div>
        </div>
        
        <div class="admin-footer">
            <p>&copy; 2023 BookStore Administration. Tous droits réservés. | 
                <a href="#">Politique de confidentialité</a> | 
                <a href="#">Conditions d'utilisation</a>
            </p>
        </div>
    </div>
    
    <script>
        // DOM Elements
        const loginCard = document.getElementById('loginCard');
        const createAccountCard = document.getElementById('createAccountCard');
        const benefitsInfo = document.getElementById('benefitsInfo');
        const createAccountLink = document.getElementById('createAccountLink');
        const backToLogin = document.getElementById('backToLogin');
        const passwordInput = document.getElementById('password');
        const passwordStrength = document.getElementById('passwordStrength');
        
        // Toggle between login and create account views
        createAccountLink.addEventListener('click', function(e) {
            e.preventDefault();
            loginCard.style.display = 'none';
            createAccountCard.style.display = 'block';
            benefitsInfo.style.display = 'none';
        });
        
        backToLogin.addEventListener('click', function(e) {
            e.preventDefault();
            loginCard.style.display = 'block';
            createAccountCard.style.display = 'none';
            benefitsInfo.style.display = 'block';
        });
        
        // Password strength indicator
        if (passwordInput) {
            passwordInput.addEventListener('input', function() {
                const password = passwordInput.value;
                let strength = 0;
                
                if (password.length >= 8) strength += 25;
                if (/[A-Z]/.test(password)) strength += 25;
                if (/[0-9]/.test(password)) strength += 25;
                if (/[^A-Za-z0-9]/.test(password)) strength += 25;
                
                passwordStrength.style.width = strength + '%';
                
                // Update color based on strength
                if (strength < 50) {
                    passwordStrength.style.background = '#dc3545'; // Rouge
                } else if (strength < 75) {
                    passwordStrength.style.background = '#ffc107'; // Jaune
                } else {
                    passwordStrength.style.background = '#28a745'; // Vert
                }
            });
        }
        
        // Auto-focus sur le premier champ
        document.addEventListener('DOMContentLoaded', function() {
            const firstInput = document.querySelector('#loginCard input[type="email"]');
            if (firstInput) {
                firstInput.focus();
            }
        });
        
        <?php if (isset($show_login) && $show_login): ?>
        // Afficher automatiquement le formulaire de connexion après création du compte
        document.addEventListener('DOMContentLoaded', function() {
            loginCard.style.display = 'block';
            createAccountCard.style.display = 'none';
            benefitsInfo.style.display = 'block';
        });
        <?php endif; ?>
    </script>
</body>
</html>