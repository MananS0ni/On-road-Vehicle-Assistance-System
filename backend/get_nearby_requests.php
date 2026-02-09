<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT sr.request_id, sr.service_type, sr.location, sr.request_time, u.full_name AS customer_name
        FROM service_requests sr
        JOIN vehicles v ON sr.vehicle_id = v.vehicle_id
        JOIN users u ON v.owner_id = u.user_id
        WHERE sr.status = 'pending'
        ORDER BY sr.request_time DESC";
$result = $conn->query($sql);

$nearby_requests = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $nearby_requests[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode(['nearby_requests' => $nearby_requests]);

$conn->close();
?>
