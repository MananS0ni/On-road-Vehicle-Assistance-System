<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$sql = "SELECT COUNT(*) AS jobs_today FROM service_history WHERE DATE(completion_time) = CURDATE()";
$result = $conn->query($sql);

$jobs_today = 0;
if ($result && $row = $result->fetch_assoc()) {
    $jobs_today = $row['jobs_today'];
}

header('Content-Type: application/json');
echo json_encode(['jobs_today' => $jobs_today]);

$conn->close();
?>
