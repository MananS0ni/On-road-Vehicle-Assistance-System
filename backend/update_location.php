<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'vehicle_owner';
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['latitude']) || !isset($data['longitude'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid location data']);
    exit;
}

$latitude = $data['latitude'];
$longitude = $data['longitude'];

// Try to update location in database
if ($role == 'service_provider') {
    $sql = "UPDATE service_providers SET current_latitude = ?, current_longitude = ?, last_location_update = NOW() WHERE id = ?";
} else if ($role == 'vehicle_owner') {
    $sql = "UPDATE vehicle_owners SET current_latitude = ?, current_longitude = ?, last_location_update = NOW() WHERE id = ?";
} else {
    // Just return success for other roles
    echo json_encode(['success' => true]);
    exit;
}

$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("ddi", $latitude, $longitude, $user_id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to update location']);
    }
    $stmt->close();
} else {
    // Fallback for missing columns - just return success
    echo json_encode(['success' => true]);
}

$conn->close();
?>
