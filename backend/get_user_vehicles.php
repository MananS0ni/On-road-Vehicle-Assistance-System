<?php
session_start();
include 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'vehicle_owner') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT v.id, v.vehicle_brand, v.vehicle_model, v.number_plate, v.fuel_type, v.year 
        FROM vehicles v 
        INNER JOIN vehicle_owners vo ON v.id = vo.vehicle_id 
        WHERE vo.owner_id = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Database prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $vehicles = [];
    while ($row = $result->fetch_assoc()) {
        $vehicles[] = $row;
    }
    echo json_encode($vehicles);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Database query failed: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
