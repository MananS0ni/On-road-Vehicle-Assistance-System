<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT SUM(p.amount) AS earnings_today FROM payments p
        JOIN jobs j ON p.job_id = j.job_id
        WHERE j.provider_id = (SELECT provider_id FROM service_providers WHERE user_id = ?) 
        AND DATE(p.payment_date) = CURDATE() AND p.status = 'completed'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$earnings_today = 0;
if ($row = $result->fetch_assoc()) {
    $earnings_today = $row['earnings_today'] ?? 0;
}

header('Content-Type: application/json');
echo json_encode(['earnings_today' => $earnings_today]);

$stmt->close();
$conn->close();
?>
