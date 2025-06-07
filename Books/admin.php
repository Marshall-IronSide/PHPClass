<?php
session_start();

// Configuration de la base de données
$host = 'localhost';
$port = '3377'; // Ajout du port personnalisé
$dbname = 'admin_db';
$username = 'root'; // Remplacez par votre nom d'utilisateur MySQL
$password = '';     // Remplacez par votre mot de passe MySQL

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
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            max-width: 1200px;
            width: 100%;
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
        
        .card-container {
            display: flex;
            justify-content: center;
            width: 100%;
            gap: 30px;
            flex-wrap: wrap;
        }
        
        .card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.25);
            width: 100%;
            max-width: 450px;
            overflow: hidden;
            transition: transform 0.4s, box-shadow 0.4s;
        }
        
        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
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
        
        .input-icon input {
            width: 100%;
            padding: 14px 14px 14px 45px;
            border: 2px solid #e1e5ee;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .input-icon input:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
        }
        
        .btn {
            display: block;
            width: 100%;
            padding: 14px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
            margin-top: 10px;
        }
        
        .btn:hover {
            background: #5a6fd8;
            transform: translateY(-2px);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .btn-secondary {
            background: #6c757d;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .btn-tertiary {
            background: transparent;
            color: #667eea;
            border: 2px solid #667eea;
        }
        
        .btn-tertiary:hover {
            background: rgba(102, 126, 234, 0.1);
        }
        
        .links {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        
        .links a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }
        
        .links a:hover {
            color: #5a6fd8;
            text-decoration: underline;
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
        
        .create-account-info {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 20px;
            max-width: 450px;
            color: #333;
        }
        
        .create-account-info h3 {
            font-size: 1.8rem;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        
        .benefits {
            list-style: none;
            margin: 25px 0;
        }
        
        .benefits li {
            margin-bottom: 15px;
            padding-left: 30px;
            position: relative;
        }
        
        .benefits li i {
            position: absolute;
            left: 0;
            top: 5px;
            color: #667eea;
            font-size: 1.2rem;
        }
        
        .password-strength {
            height: 5px;
            background: #e1e5ee;
            border-radius: 3px;
            margin-top: 8px;
            overflow: hidden;
        }
        
        .strength-meter {
            height: 100%;
            width: 0;
            background: #dc3545;
            transition: width 0.3s, background 0.3s;
        }
        
        .alert {
            padding: 12px;
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
        
        .stats-info {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 15px;
            color: white;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .stats-info h4 {
            margin-bottom: 10px;
            font-size: 1.2rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        
        .stat-item {
            background: rgba(255, 255, 255, 0.1);
            padding: 10px;
            border-radius: 8px;
            text-align: center;
        }
        
        .stat-number {
            font-size: 1.5rem;
            font-weight: bold;
            display: block;
        }
        
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.8;
        }
        
        @media (max-width: 768px) {
            .card-container {
                flex-direction: column;
                align-items: center;
            }
            
            .create-account-info {
                margin-top: 30px;
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
        
        <?php 
        // Affichage des statistiques (optionnel)
        $stats = getAdminStats($pdo);
        if ($stats): 
        ?>
        <div class="stats-info">
            <h4>Statistiques de l'administration</h4>
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-number"><?php echo $stats['total_admins']; ?></span>
                    <span class="stat-label">Total Admins</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo $stats['active_admins']; ?></span>
                    <span class="stat-label">Actifs</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo $stats['recent_logins']; ?></span>
                    <span class="stat-label">Connexions récentes</span>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="card-container">
            <!-- Login Card -->
            <div class="card" id="loginCard">
                <div class="card-header">
                    <h2>Connexion Administration</h2>
                    <p>Accédez au panneau d'administration BookStore</p>
                </div>
                <div class="card-body">
                    <?php if (isset($login_error)): ?>
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-triangle"></i>
                            <?php echo $login_error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($success_message)): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <?php echo $success_message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form id="loginForm" method="POST" action="">
                        <div class="form-group">
                            <label for="loginEmail">Adresse e-mail</label>
                            <div class="input-icon">
                                <i class="fas fa-envelope"></i>
                                <input type="email" id="loginEmail" name="admin_username" placeholder="admin@bookstore.com" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="loginPassword">Mot de passe</label>
                            <div class="input-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password" id="loginPassword" name="admin_password" placeholder="Entrez votre mot de passe" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn" name="admin_login">
                                <i class="fas fa-sign-in-alt"></i> Se connecter
                            </button>
                        </div>
                        
                        <div class="links">
                            <a href="#" id="forgotPassword">Mot de passe oublié ?</a>
                            <a href="#" id="createAccountLink">Créer un compte admin</a>
                        </div>
                    </form>
                    
                    <div class="form-group" style="margin-top: 30px;">
                        <a href="index.php" class="btn btn-tertiary">
                            <i class="fas fa-home"></i> Retour à l'accueil
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Create Account Card (Hidden by default) -->
            <div class="card" id="createAccountCard" style="display: none;">
                <div class="card-header">
                    <h2>Créer un compte admin</h2>
                    <p>Nouveau compte d'administration</p>
                </div>
                <div class="card-body">
                    <?php if (isset($create_error)): ?>
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-triangle"></i>
                            <?php echo $create_error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form id="createAccountForm" method="POST" action="">
                        <div class="form-group">
                            <label for="fullName">Nom complet</label>
                            <div class="input-icon">
                                <i class="fas fa-user"></i>
                                <input type="text" id="fullName" name="full_name" placeholder="Entrez votre nom complet" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Adresse e-mail</label>
                            <div class="input-icon">
                                <i class="fas fa-envelope"></i>
                                <input type="email" id="email" name="email" placeholder="Entrez votre e-mail" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Mot de passe</label>
                            <div class="input-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password" id="password" name="password" placeholder="Créez un mot de passe" required>
                            </div>
                            <div class="password-strength">
                                <div class="strength-meter" id="passwordStrength"></div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirmPassword">Confirmez le mot de passe</label>
                            <div class="input-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password" id="confirmPassword" name="confirm_password" placeholder="Confirmez votre mot de passe" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn" name="create_account">
                                <i class="fas fa-user-plus"></i> Créer le compte
                            </button>
                        </div>
                    </form>
                    
                    <div class="links">
                        <a href="#" id="backToLogin">Retour à la connexion</a>
                    </div>
                    
                    <div class="form-group" style="margin-top: 20px;">
                        <a href="index.php" class="btn btn-tertiary">
                            <i class="fas fa-home"></i> Retour à l'accueil
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Admin Benefits -->
            <div class="create-account-info" id="benefitsInfo">
                <h3>Administration BookStore</h3>
                <ul class="benefits">
                    <li><i class="fas fa-cogs"></i> Gestion complète du catalogue de livres</li>
                    <li><i class="fas fa-users"></i> Administration des comptes utilisateurs</li>
                    <li><i class="fas fa-chart-line"></i> Tableaux de bord et statistiques</li>
                    <li><i class="fas fa-shopping-cart"></i> Suivi des commandes et ventes</li>
                    <li><i class="fas fa-shield-alt"></i> Sécurité et contrôle d'accès</li>
                </ul>
                <p>Interface d'administration sécurisée pour gérer efficacement votre librairie en ligne BookStore.</p>
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