<?php include 'db.php'; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des articles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4 text-primary">Ajouter ou Modifier une vente</h2>
    <form method="POST" action="process.php" class="card p-4 shadow-sm">
        <input type="hidden" name="id" value="<?= isset($_GET['edit']) ? $_GET['edit'] : '' ?>">

        <div class="mb-3">
            <label class="form-label">Nom de l'article</label>
            <input type="text" name="product" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Prix (€)</label>
            <input type="number" name="amount" step="0.01" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Date vente</label>
            <input type="datetime" name="created_at" class="form-control" required>
        </div>

        <button type="submit" name="<?= isset($_GET['edit']) ? 'modifier' : 'ajouter' ?>"
                class="btn btn-<?= isset($_GET['edit']) ? 'warning' : 'success' ?>">
            <?= isset($_GET['edit']) ? 'Modifier' : 'Ajouter' ?>
        </button>
    </form>

    <hr class="my-5">

    <h2 class="mb-4 text-primary">Articles vendus</h2>
    <table class="table table-bordered table-hover bg-white shadow-sm">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prix (€)</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $pdo->query("SELECT * FROM orders");
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {

            //while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['product']}</td>
                        <td>{$row['amount']}</td>
                        <td>{$row['created_at']}</td>
                        <td>
                            <a href='ajout.php?edit={$row['id']}' class='btn btn-sm btn-warning'>Modifier</a>
                            <a href='process.php?supprimer={$row['id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Supprimer cet article ?\")'>Supprimer</a>
                        </td>
                      </tr>";
            }
            ?>
        </tbody>
    </table>
</div>
<br/><a href="index.php">Retour</a>
</body>
</html>