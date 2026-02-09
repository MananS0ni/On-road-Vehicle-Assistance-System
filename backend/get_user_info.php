<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized - No session']);
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Using YOUR EXACT table names
if ($role == 'vehicle_owner') {
    $sql = "SELECT name, email, phone_number FROM vehicle_owners WHERE id = ?";
} else if ($role == 'service_provider') {
    $sql = "SELECT name, email, business_phone as phone_number, business_name, rating FROM service_providers WHERE id = ?";
} else if ($role == 'admin') {
    $sql = "SELECT name, email FROM admins WHERE id = ?";
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid role: ' . $role]);
    exit;
}

$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Database prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user_data = $result->fetch_assoc();
    echo json_encode(['success' => true, 'user' => $user_data, 'role' => $role]);
} else {
    echo json_encode(['success' => false, 'error' => 'User not found in ' . $role . ' table']);
}

$stmt->close();
$conn->close();
?>
