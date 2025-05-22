<?php

// Connexion à la base de données
$host = 'localhost';
$db = 'dashboard_db';
$user = 'root';
$pass = '';
$port = '3377';
$conn = new mysqli($host, $user, $pass, $db,$port);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Échec de connexion : " . $conn->connect_error);
}

// Traitement du formulaire
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users(username, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        echo "Inscription réussie !";
    } else {
        echo "Erreur : " . $stmt->error;
    }

    $stmt->close();
}
?>

<!-- Formulaire HTML -->
<!DOCTYPE html>
<html>
<head>
    <title>Inscription</title>
</head>
<body>
    <h2>Formulaire d'inscription</h2>
    <form method="post" action="register.php">
        Nom d'utilisateur : <input type="text" name="username" required><br><br>
        Email : <input type="email" name="email" required><br><br>
        Mot de passe : <input type="password" name="password" required><br><br>
        <input type="submit" value="S'inscrire">
    </form>
    <br/><a href="logi.php">Se connecter</a>
</body>
</html>
