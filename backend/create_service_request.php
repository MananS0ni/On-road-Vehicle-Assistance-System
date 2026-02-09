<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized - No session']);
    exit;
}

if ($_SESSION['role'] !== 'vehicle_owner') {
    http_response_code(403);
    echo json_encode(['error' => 'Only vehicle owners can create requests']);
    exit;
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON data']);
    exit;
}

$service_type = $data['service_type'] ?? '';
$location = $data['location'] ?? '';

if (empty($service_type) || empty($location)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing service_type or location']);
    exit;
}

// Get user's first vehicle using YOUR EXACT table structure
$vehicle_id = null;
$sql_vehicle = "SELECT vehicle_id FROM vehicles WHERE owner_id = ? LIMIT 1";
$stmt_vehicle = $conn->prepare($sql_vehicle);

if (!$stmt_vehicle) {
    http_response_code(500);
    echo json_encode(['error' => 'Database prepare failed: ' . $conn->error]);
    exit;
}

$stmt_vehicle->bind_param("i", $user_id);
$stmt_vehicle->execute();
$result_vehicle = $stmt_vehicle->get_result();

if ($result_vehicle->num_rows > 0) {
    $row_vehicle = $result_vehicle->fetch_assoc();
    $vehicle_id = $row_vehicle['vehicle_id'];
} else {
    $stmt_vehicle->close();
    http_response_code(400);
    echo json_encode(['error' => 'No vehicle found for user ID: ' . $user_id]);
    exit;
}
$stmt_vehicle->close();

// Insert service request using YOUR EXACT table structure
$sql = "INSERT INTO service_requests (vehicle_id, service_type, location, status, request_time) VALUES (?, ?, ?, 'pending', NOW())";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Database prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("iss", $vehicle_id, $service_type, $location);

if ($stmt->execute()) {
    $request_id = $stmt->insert_id;
    echo json_encode(['success' => true, 'request_id' => $request_id]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to create request: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
