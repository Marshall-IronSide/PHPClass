<?php
$host = 'localhost';
$db = 'exemple_utilisateurs';
$user = 'root';
$pass = '';
$port = 3377;

$conn = new mysqli($host, $user, $pass, $db, $port);

// Vérifie la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Traitement du formulaire
session_start();
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    $sql = "SELECT id, password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);

    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            // Connexion réussie
            $_SESSION["user_id"] = $id;
            $_SESSION["username"] = $username;
            header("Location: welcome.php");
            exit;
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
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f2f2f2;
        }
        .container {
            width: 300px;
            padding: 16px;
            background-color: white;
            margin: 50px auto;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .Avatar {
            display: block;
            margin: 0 auto 20px;
            width: 100px;
            height: 100px;
            border-radius: 50%;
        }
        input[type=text], input[type=password] {
            width: 100%;
            padding: 12px 20px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            box-sizing: border-box;
            border-radius: 5px;
        }
        button {
            background-color: #04AA6D;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            cursor: pointer;
            width: 100%;
            border-radius: 5px;
        }
        button:hover {
            opacity: 0.8;
        }
        .message {
            color: red;
            margin-bottom: 10px;
            text-align: center;
        }
        .register-link {
            text-align: center;
            margin-top: 10px;
        }
        .register-link a {
            color: #04AA6D;
            text-decoration: none;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="Avatar.jpg" alt="Avatar" class="Avatar">
        <?php if (!empty($message)) echo "<p class='message'>$message</p>"; ?>
        <form method="post" action="logi.php">
            <label for="username"><b>Nom d'utilisateur</b></label>
            <input type="text" placeholder="Entrez votre nom d'utilisateur" name="username" required>

            <label for="password"><b>Mot de passe</b></label>
            <input type="password" placeholder="Entrez votre mot de passe" name="password" required>

            <button type="submit">Se connecter</button>
        </form>
        <div class="register-link">
            <p>Pas encore inscrit ? <a href="register.php">Inscrivez-vous ici</a>.</p>
        </div>
    </div>
</body>
</html>