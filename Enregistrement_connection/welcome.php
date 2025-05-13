<?php
session_start();
if(!isset($_SESSION['username'])) {
    // Si l'utilisateur n'est pas connecté, redirigez-le vers la page de connexion
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head><title>Bienvenue</title></head>
<body>
    <h1>Bienvenue, <?php echo htmlspecialchars($_SESSION["username"]); ?> !</h1>
    <p><a href="logout.php">Déconnexion</a></p>
</body>
</html>