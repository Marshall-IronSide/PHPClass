<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Fiche d'Inscription Licence</title>
</head>

<body>
    <form action="Recupération.php" method="post">
        <fieldset>
            <legend>
                <h1>Fiche d'Inscription Licence</h1>
            </legend>

            <label for="date">Date :</label>
            <input type="date" name="date"><br><br>

            <label for="filiere">Filière :</label>
            <input type="text" name="filiere" required><br><br>

            <label for="niveau">Niveau :</label>
            <input type="text" name="niveau" required><br><br>

            <label for="nom">Nom :</label>
            <input type="text" name="nom" required><br><br>

            <label for="prenoms">Prénoms :</label>
            <input type="text" name="prenoms" required><br><br>

            <label>Sexe :</label>
            M <input type="radio" name="sexe" value="M" required>
            F <input type="radio" name="sexe" value="F" required><br><br>

            <label for="tel">Tél :</label>
            <input type="tel" name="tel" required><br><br>

            <label for="email">E-mail :</label>
            <input type="email" name="email" required><br><br>

            <label for="etablissement">Etablissement de provenance :</label>
            <input type="text" name="etablissement" required><br><br>

            <label for="religion">Religion :</label>
            <input type="text" name="religion"><br><br>

            <label for="date_naissance">Date de naissance :</label>
            <input type="date" name="date_naissance" required><br><br>

            <label for="lieu_naissance">Lieu de naissance :</label>
            <input type="text" name="lieu_naissance" required=""><br><br>

            <label for="prefecture">Préfecture :</label>
            <input type="text" name="prefecture"><br><br>

            <label for="nationalite">Nationalité :</label>
            <input type="text" name="nationalite"><br><br>

            <div class="filiere-section">
                <h2 class="license-title">LICENCE</h2>
                <p class="choose-subtitle">CHOISISSEZ VOTRE FILIERE (cochez)</p>
                <div class="filiere-options">
                    <label class="filiere-option">
                        <span>Développement d’Application</span>
                        <input type="radio" name="filiere_choisie" value="Développement d’Application">
                    </label>
                    <label class="filiere-option">
                        <span>Technologie Informatique de Gestion</span>
                        <input type="radio" name="filiere_choisie" value="Technologie Informatique de Gestion">
                    </label>
                    <label class="filiere-option">
                        <span>Commerce International de MBS Paris</span>
                        <input type="radio" name="filiere_choisie" value="Commerce International de MBS Paris">
                    </label>
                    <label class="filiere-option">
                        <span>Management Industriel et Logistique (MIL) Maroc</span>
                        <input type="radio" name="filiere_choisie" value="Management Industriel et Logistique (MIL) Maroc">
                    </label>
                    <label class="filiere-option">
                        <span>Ingénierie des Systèmes Informatiques (ISI) Maroc</span>
                        <input type="radio" name="filiere_choisie" value="Ingénierie des Systèmes Informatiques (ISI) Maroc">
                    </label>
                </div>
            </div>
            <fieldset>
                <legend>PIECES A FOURNIR (Cochez pour confirmer)</legend>

                <input type="checkbox" name="pieces[]" value="Photocopie légalisée de l’acte de naissance" required>
                <label for="piece1">Une photocopie légalisée de l’acte de naissance</label><br>

                <input type="checkbox" name="pieces[]" value="Photocopie légalisée de l’attestation ou relevé du BAC II" required>
                <label for="piece2">Une photocopie légalisée de l’attestation ou relevé du BAC II</label><br>

                <input type="checkbox" name="pieces[]" value="Reçu des droits d’inscription" required>
                <label for="piece3">Une photocopie du reçu attestant le versement des droits d’inscription</label><br>

                <input type="checkbox" name="pieces[]" value="Photos d’identité" required>
                <label for="piece4">Quatre photos d’identité</label><br>
            </fieldset>

            <input type="submit" value="ENVOYEZ">
        </fieldset>
    </form>

</body>

</html>