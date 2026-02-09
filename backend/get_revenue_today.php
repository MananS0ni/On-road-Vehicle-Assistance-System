<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$sql = "SELECT SUM(cost) AS revenue_today FROM service_history WHERE DATE(completion_time) = CURDATE()";
$result = $conn->query($sql);

$revenue_today = 0;
if ($result && $row = $result->fetch_assoc()) {
    $revenue_today = $row['revenue_today'] ?? 0;
}

header('Content-Type: application/json');
echo json_encode(['revenue_today' => $revenue_today]);

$conn->close();
?>
