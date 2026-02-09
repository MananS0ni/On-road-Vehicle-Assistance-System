<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT COUNT(*) AS jobs_today FROM service_requests 
        WHERE provider_id = ? 
        AND DATE(request_time) = CURDATE() AND job_status = 'completed'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$jobs_today = 0;
if ($row = $result->fetch_assoc()) {
    $jobs_today = $row['jobs_today'];
}

header('Content-Type: application/json');
echo json_encode(['jobs_today' => $jobs_today]);

$stmt->close();
$conn->close();
?>
