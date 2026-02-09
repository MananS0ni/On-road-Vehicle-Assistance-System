<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

if ($role == 'vehicle_owner') {
    // Get active request for vehicle owner - FIXED QUERY
    $sql = "SELECT sr.*, sp.name as provider_name, sp.business_phone, sp.business_name,
                   sp.current_latitude as provider_lat, sp.current_longitude as provider_lng,
                   vo.current_latitude as owner_lat, vo.current_longitude as owner_lng,
                   v.vehicle_brand, v.vehicle_model, v.number_plate
            FROM service_requests sr 
            LEFT JOIN service_providers sp ON sr.provider_id = sp.id
            JOIN vehicles v ON sr.vehicle_id = v.vehicle_id
            JOIN vehicle_owners vo ON v.owner_id = vo.id
            WHERE vo.id = ? AND sr.status IN ('pending', 'accepted', 'en_route', 'arrived', 'in_progress') 
            ORDER BY sr.request_time DESC LIMIT 1";
    
} else if ($role == 'service_provider') {
    // Get active job for service provider - FIXED QUERY  
    $sql = "SELECT sr.*, vo.name as owner_name, vo.phone_number, 
                   sp.current_latitude as provider_lat, sp.current_longitude as provider_lng,
                   vo.current_latitude as owner_lat, vo.current_longitude as owner_lng,
                   v.vehicle_brand, v.vehicle_model, v.number_plate
            FROM service_requests sr 
            JOIN vehicles v ON sr.vehicle_id = v.vehicle_id
            JOIN vehicle_owners vo ON v.owner_id = vo.id
            LEFT JOIN service_providers sp ON sr.provider_id = sp.id
            WHERE sr.provider_id = ? AND sr.status IN ('accepted', 'en_route', 'arrived', 'in_progress') 
            ORDER BY sr.request_time DESC LIMIT 1";
    
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid role']);
    exit;
}

$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("SQL prepare error: " . $conn->error);
    http_response_code(500);
    echo json_encode(['error' => 'Database prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $tracking_data = $result->fetch_assoc();
    error_log("Tracking data found for user " . $user_id . " role " . $role . ": " . json_encode($tracking_data));
    echo json_encode(['success' => true, 'data' => $tracking_data]);
} else {
    error_log("No tracking data found for user: " . $user_id . " role: " . $role . ". Query: " . $sql);
    echo json_encode(['success' => true, 'data' => null]);
}

$stmt->close();
$conn->close();
?>
