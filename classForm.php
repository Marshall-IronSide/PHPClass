<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire HTML</title>
    <link rel="stylesheet" href="styles.css"> <!-- Lien vers le fichier CSS -->
</head>
<body>
    <h1>Formulaire HTML</h1>
    <form action="classForm.php" method="POST">
        <label>Prénom : </label>
        <input type="text" name="prenom" required><br>

        <label>Email : </label>
        <input type="email" name="email" required><br>

        <label>Âge : </label>
        <input type="number" name="age" required><br>

        <label>Sexe : </label>
        <input type="radio" name="sexe" value="Femme"> Femme
        <input type="radio" name="sexe" value="Homme"> Homme
        <input type="radio" name="sexe" value="Autre"> Autre<br>

        <label>Pays de résidence : </label>
        <select name="pays">
            <option value="France">France</option>
            <option value="Togo">Togo</option>
            <option value="Belgique">Belgique</option>
        </select><br>

        <input type="submit" value="Envoyer">
    </form>
</body>
</html>