<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$request_id = $_GET['request_id'];

$sql = "SELECT sr.*, v.vehicle_brand, v.vehicle_model, vo.name AS customer_name
        FROM service_requests sr
        JOIN vehicles v ON sr.vehicle_id = v.vehicle_id
        JOIN vehicle_owners vo ON v.owner_id = vo.id
        WHERE sr.request_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $request_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $request = $result->fetch_assoc();
    echo json_encode(['request' => $request]);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Request not found']);
}

$stmt->close();
$conn->close();
?>