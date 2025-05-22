<?php
$host = 'localhost';
$db = 'exemple_utilisateurs';
$user = 'root';
$pass = '';
$port = 3377;

// Connexion à la base de données
$conn = new mysqli($host, $user, $pass, $db, $port);

// Vérifiez la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);

    // Préparez et exécutez la requête d'insertion
    $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        echo "<p style='color: green; text-align: center;'>Inscription réussie pour $username</p>";
    } else {
        echo "<p style='color: red; text-align: center;'>Erreur : " . $stmt->error . "</p>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inscription</title>
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
        input[type=text], input[type=password], input[type=email] {
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
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 50px;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            border: 1px solid #888;
            width: 80%;
            border-radius: 10px;
            padding: 20px;
        }
        .close {
            position: absolute;
            right: 25px;
            top: 10px;
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Inscription</h2>
    <button onclick="document.getElementById('id01').style.display='block'" style="width:auto; display: block; margin: 20px auto;">S'inscrire</button>

    <div id="id01" class="modal">
        <div class="modal-content">
            <span onclick="document.getElementById('id01').style.display='none'" class="close" title="Fermer">&times;</span>
            <form method="post" action="regis.php">
                <div class="container">
                    <img src="Avatar.jpg" alt="Avatar" class="Avatar">
                    <h1 style="text-align: center;">Créer un compte</h1>
                    <p style="text-align: center;">Veuillez remplir ce formulaire pour créer un compte.</p>
                    <hr>
                    <label for="username"><b>Nom d'utilisateur</b></label>
                    <input type="text" placeholder="Entrez votre nom d'utilisateur" name="username" required>

                    <label for="email"><b>Email</b></label>
                    <input type="email" placeholder="Entrez votre email" name="email" required>

                    <label for="password"><b>Mot de passe</b></label>
                    <input type="password" placeholder="Entrez votre mot de passe" name="password" required>

                    <label>
                        <input type="checkbox" checked="checked" name="remember" style="margin-bottom:15px"> Se souvenir de moi
                    </label>

                    <p>En créant un compte, vous acceptez nos <a href="#" style="color:dodgerblue">Conditions & Confidentialité</a>.</p>

                    <div class="clearfix">
                        <button type="button" onclick="document.getElementById('id01').style.display='none'" class="cancelbtn" style="background-color: #f44336; width: 48%; float: left;">Annuler</button>
                        <button type="submit" class="signupbtn" style="width: 48%; float: right;">S'inscrire</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Get the modal
        var modal = document.getElementById('id01');

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>