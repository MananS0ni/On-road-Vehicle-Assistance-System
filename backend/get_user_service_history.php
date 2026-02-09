<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT sr.* FROM service_requests sr JOIN vehicles v ON sr.vehicle_id = v.vehicle_id WHERE v.owner_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$service_history = [];
while ($row = $result->fetch_assoc()) {
    $service_history[] = $row;
}

header('Content-Type: application/json');
echo json_encode(['service_history' => $service_history]);

$stmt->close();
$conn->close();
?>
