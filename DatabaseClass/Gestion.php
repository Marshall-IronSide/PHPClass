<?php

/******************************************************************************************************
                             MINI APPLICATION PHP+MYSQL (MARIADB)
                            AUTEUR: [Marshall Ironside]
 ******************************************************************************************************/

//configuration de la base de données

$host = 'localhost'; // ou l'adresse IP de votre serveur de base de données
$port = 3377;
$username = 'root'; // nom d'utilisateur de la base de données  
$password = '13187500'; // mot de passe de la base de données
$dbname = 'gestion_vente'; // nom de la base de données

try {
    // Connect without database name first
    $pdo = new PDO("mysql:host=$host;port=$port", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Now create the database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    
    // Now connect to the database
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}

/***********************************************************************************************************
                                        CREATION DE BASE DE DONNEES 
 ************************************************************************************************************/

//$pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
//$pdo->exec("USE $dbname");

/***********************************************************************************************************
                                        CREATION TABLE CLIENTS
 ***********************************************************************************************************/
$pdo->exec("CREATE TABLE IF NOT EXISTS client (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    telephone VARCHAR(20),
    date_inscrip DATE NOT NULL
)");

/***********************************************************************************************************
                                        CREATION TABLE COMMANDE
 ***********************************************************************************************************/

$pdo->exec("CREATE TABLE IF NOT EXISTS commande (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_client INT NOT NULL,
    date_cmd DATE NOT NULL,
    montant DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (id_client) REFERENCES client(id)
)");

/************************************************************************************
                                        ALTER TABLE (Ajout colonne)
 ************************************************************************************/

try {
    $pdo->exec("ALTER TABLE client ADD COLUMN adresse VARCHAR(100)");
} catch (Exception $e) {
}

/************************************************************************************
                                        INSERTION CLIENT
 ************************************************************************************/

if (isset($_POST['ajouter'])) {
    $sql = "INSERT INTO client (nom, prenom, email, telephone, date_inscrip, adresse) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $_POST['nom'],
        $_POST['prenom'],
        $_POST['email'],
        $_POST['telephone'],
        $_POST['date_inscrip'],
        $_POST['adresse']
    ]);
}

/************************************************************************************
                                        SUPPRESSION
 ************************************************************************************/

if (isset($_GET['supprimer'])) {
    $id = $_GET['supprimer'];
    $stmt = $pdo->prepare("DELETE FROM client WHERE id = ?");
    $stmt->execute([$id]);
}

/************************************************************************************
                                        MODIFICATION
 ************************************************************************************/

if (isset($_POST['modifier'])) {
    $sql = "UPDATE client SET nom = ?, prenom = ?, email = ?, telephone = ?, date_inscrip = ?, adresse = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $_POST['nom'],
        $_POST['prenom'],
        $_POST['email'],
        $_POST['telephone'],
        $_POST['date_inscrip'],
        $_POST['adresse'],
        $_POST['id']
    ]);
}

/************************************************************************************
                                        Récupération de données
 ************************************************************************************/
$clients = $pdo->query("SELECT * FROM client")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Mini Gestion vente</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <h2>
        MINI APPLICATION PHP+MYSQL (MARIADB) - GESTION DE VENTE
    </h2>
    <form method="POST">
        <h3>
            Ajouter/Modifier un client
        </h3>
        <input type="hidden" name="id" id="id">
        <input type="text" name="nom" id="nom" placeholder="Nom" required>
        <input type="text" name="prenom" id="prenom" placeholder="Prénom" required>
        <input type="email" name="email" id="email" placeholder="Email" required>
        <input type="text" name="telephone" id="telephone" placeholder="Téléphone">
        <input type="date" name="date_inscrip" id="date_inscrip" placeholder="Date d'inscription" required>
        <input type="text" name="adresse" id="adresse" placeholder="Adresse">
        <button type="submit" name="ajouter">Ajouter</button>
        <button type="submit" name="modifier">Modifier</button>
    </form>
    <!-- Liste des clients -->
    <table>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Email</th>
            <th>Téléphone</th>
            <th>Date d'inscription</th>
            <th>Adresse</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($clients as $c): ?>
            <tr>
                <td><?= htmlspecialchars($c['id'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($c['nom'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($c['prenom'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($c['email'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($c['telephone'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($c['date_inscrip'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($c['adresse'], ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                    <a href="?supprimer=<?= htmlspecialchars($c['id'], ENT_QUOTES, 'UTF-8') ?>" class="btn-delete">Supprimer</a>
                    <a href="#" onclick="remplir('<?= htmlspecialchars($c['id'], ENT_QUOTES, 'UTF-8') ?>', '<?= htmlspecialchars($c['nom'], ENT_QUOTES, 'UTF-8') ?>', '<?= htmlspecialchars($c['prenom'], ENT_QUOTES, 'UTF-8') ?>', '<?= htmlspecialchars($c['email'], ENT_QUOTES, 'UTF-8') ?>', '<?= htmlspecialchars($c['telephone'], ENT_QUOTES, 'UTF-8') ?>', '<?= htmlspecialchars($c['date_inscrip'], ENT_QUOTES, 'UTF-8') ?>', '<?= htmlspecialchars($c['adresse'], ENT_QUOTES, 'UTF-8') ?>')" class="btn-edit">Modifier</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <script>
        function remplir(id, nom, prenom, email, telephone, date_inscrip, adresse) {
            document.getElementById('id').value = id;
            document.getElementById('nom').value = nom;
            document.getElementById('prenom').value = prenom;
            document.getElementById('email').value = email;
            document.getElementById('telephone').value = telephone;
            document.getElementById('date_inscrip').value = date_inscrip;
            document.getElementById('adresse').value = adresse;
        }
    </script>
</body>

</html>