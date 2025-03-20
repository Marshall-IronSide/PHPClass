<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST['date'];
    $filiere = $_POST['filiere'];
    $niveau = $_POST['niveau'];
    $nom = $_POST['nom'];
    $prenoms = $_POST['prenoms'];
    $sexe = $_POST['sexe'];
    $tel = $_POST['tel'];
    $email = $_POST['email'];
    $etablissement = $_POST['etablissement'];
    $religion = $_POST['religion'];
    $date_naissance = $_POST['date_naissance'];
    $lieu_naissance = $_POST['lieu_naissance'];
    $prefecture = $_POST['prefecture'];
    $nationalite = $_POST['nationalite'];
    $filiere_choisie = $_POST['filiere_choisie'];
    $pieces = $_POST['pieces'] ?? [];

    echo "<h1>Données reçues :</h1>";
    echo "<p>Date : $date</p>";
    echo "<p>Filière : $filiere</p>";
    echo "<p>Niveau : $niveau</p>";
    echo "<p>Nom : $nom</p>";
    echo "<p>Prénoms : $prenoms</p>";
    echo "<p>Sexe : $sexe</p>";
    echo "<p>Tél : $tel</p>";
    echo "<p>E-mail : $email</p>";
    echo "<p>Etablissement de provenance : $etablissement</p>";
    echo "<p>Religion : $religion</p>";
    echo "<p>Date de naissance : $date_naissance</p>";
    echo "<p>Lieu de naissance : $lieu_naissance</p>";
    echo "<p>Préfecture : $prefecture</p>";
    echo "<p>Nationalité : $nationalite</p>";
    echo "<p>Filière choisie : $filiere_choisie</p>";
    echo "<p>Pièces à fournir reçues :</p><ul>";
    foreach ($pieces as $piece) {
        echo "<li>$piece</li>";
    }
    echo "</ul>";
}
?>