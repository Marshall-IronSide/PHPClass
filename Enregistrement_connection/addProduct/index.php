<?php include 'data.php'; ?>
<?php
session_start();
$username=isset($_SESSION["username"]) ? $_SESSION["username"]: null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard PHP</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f6f8;
        margin: 0;
        padding: 40px;
        text-align: center;
    }

    h2 {
        color: #333;
        margin-bottom: 30px;
    }

    .chart-container {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        max-width: 700px;
        margin: auto;
    }

    canvas {
        margin-top: 20px;
    }
</style>

</head>
<body>
<?php if($username): ?>
        <h1>Bienvenue, <?= htmlspecialchars($username)?> !</h1>
        
        <?php else:?>
            <p>Vous n'êtes pas connectés. <a href="login.php">Connexion</a></p>
            <?php endif;?>
    <h1>Mon Dashboard</h1>
    <div class="cards">
        <div class="card">
            <h2>Utilisateurs</h2>
            <p><?php echo $stats['users']; ?></p>
        </div>
        <div class="card">
            <h2>Commandes</h2>
            <p><?php echo $stats['orders']; ?></p>
        </div>
        <div class="card">
            <h2>Revenus</h2>
            <p><?php echo number_format($stats['revenue'], 2, ',', ' '); ?> €</p>
        </div>
    </div>
<h2>Revenus mensuels</h2>
    <div class="chart-container">
        <canvas id="revenueChart" width="600" height="300"></canvas>
    </div>
    <br>
    <a href="ajout.php" class="ajout-btn">Enregistrer une nouvelle commande ou mettre à jour une commande</a><br>
    <a href="logout.php" class="logout-btn">Déconnexion</a>
<script>
fetch('chart-data.php')
    .then(response => response.json())
    .then(data => {
        const labels = data.map(row => row.month);
        const revenues = data.map(row => row.total_revenue);

        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Revenus (€)',
                    data: revenues,
                    backgroundColor: 'rgb(7, 199, 64)',
                    borderColor: 'rgb(69, 235, 54)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>

</body>
</html>