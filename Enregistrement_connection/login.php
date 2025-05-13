<?php
// Connection à la base de données
$host = 'localhost';
$db = 'exemple_utilisateurs';
$user = 'root';
$pass = '';
$port = 3377;

$conn = new mysqli($host, $user, $pass, $db, $port);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Echec de connexion: " . $conn->connect_error);
}

// Traitement du formulaire de connexion
session_start();
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $sql = "SELECT id, password FROM users WHERE username = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);

    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            // Authentification réussie
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            header("Location: welcome.php");
            exit();
        } else {
            $message = "Nom d'utilisateur ou mot de passe incorrect.";
        }
    } else {
        $message = "Nom d'utilisateur ou mot de passe incorrect.";
    }
    $stmt->close();
}
?>

<!-- Formulaire HTML -->
<!DOCTYPE html>
<html>
<head>
    <title>Connexion</title>
</head>
<body>
    <h2>Connexion</h2>
    <?php if (!empty($message)) echo "<p style='color:red;'>$message</p>"; ?>
    <form method="post" action="login.php">
        Nom d'utilisateur : <input type="text" name="username" required><br><br>
        Mot de passe : <input type="password" name="password" required><br><br>
        <input type="submit" value="Connexion">
    </form>
</body>
</html>