<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $prenom = htmlspecialchars($_POST["prenom"]);
    $email = htmlspecialchars($_POST["email"]);
    $age = (int) $_POST["age"];
    $sexe = htmlspecialchars($_POST["sexe"]);
    $pays = htmlspecialchars($_POST["pays"]);

    echo "<h1>Informations reçues :</h1>";
    echo "Prénom : " . $prenom . "<br>";
    echo "Email : " . $email . "<br>";
    echo "Âge : " . $age . "<br>";
    echo "Sexe : " . $sexe . "<br>";
    echo "Pays de résidence : " . $pays . "<br>";
} else {
    echo "Aucune donnée reçue.";
}
?>