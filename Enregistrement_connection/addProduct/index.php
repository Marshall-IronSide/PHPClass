<?php include 'data.php'; ?>
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
    <br/><a href="ajout.php"> Enregistrer une nouvelle commande ou mettre a jour une commande</a>
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
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
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