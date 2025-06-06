<?php
include 'db.php';

$sql = "
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') AS month,
        SUM(amount) AS total_revenue
    FROM orders
    GROUP BY month
    ORDER BY month ASC
";

$stmt = $pdo->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Retourner les données en JSON
header('Content-Type: application/json');
echo json_encode($data);
exit;
?>