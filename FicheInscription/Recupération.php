<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation d'inscription</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="confirmation-container">
        <h1>Confirmation de votre inscription</h1>

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

            echo '<div class="confirmation-item">';
            echo '<strong>🗓 Date :</strong> ' . htmlspecialchars($date);
            echo '</div>';

            echo '<div class="confirmation-item">';
            echo '<strong>👤 Informations personnelles :</strong><br>';
            echo htmlspecialchars($nom) . ' ' . htmlspecialchars($prenoms) . '<br>';
            echo 'Sexe : ' . htmlspecialchars($sexe) . '<br>';
            echo 'Né(e) le : ' . htmlspecialchars($date_naissance) . ' à ' . htmlspecialchars($lieu_naissance);
            echo '</div>';

            echo '<div class="confirmation-item">';
            echo '<strong>📚 Parcours académique :</strong><br>';
            echo 'Filière choisie : ' . htmlspecialchars($filiere_choisie) . '<br>';
            echo 'Niveau : ' . htmlspecialchars($niveau) . '<br>';
            echo 'Établissement précédent : ' . htmlspecialchars($etablissement);
            echo '</div>';

            echo '<div class="confirmation-item">';
            echo '<strong>📄 Pièces fournies :</strong><br>';
            echo '<ul>';
            foreach ($pieces as $piece) {
                echo '<li>' . htmlspecialchars($piece) . '</li>';
            }
            echo '</ul>';
            echo '</div>';
        }
        ?>

        <p class="form-group" style="text-align: center; margin-top: 2rem;">
            <a href="Inscription.php" class="retour-btn">Retour au formulaire</a>
        </p>
    </div>
</body>

</html>