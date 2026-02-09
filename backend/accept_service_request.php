<?php
session_start();
include 'db_connect.php';

// Debug session information
error_log("Accept job called. Session: " . print_r($_SESSION, true));

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No session found', 'debug' => $_SESSION]);
    exit;
}

if ($_SESSION['role'] != 'service_provider') {
    http_response_code(401);
    echo json_encode(['error' => 'Not a service provider. Current role: ' . $_SESSION['role']]);
    exit;
}

$provider_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['request_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing request_id']);
    exit;
}

$request_id = $data['request_id'];

// Assign the provider to the request and update status
$sql = "UPDATE service_requests SET provider_id = ?, status = 'accepted', accepted_time = NOW() WHERE request_id = ? AND status = 'pending'";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Database prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("ii", $provider_id, $request_id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    echo json_encode(['success' => true, 'message' => 'Job accepted successfully']);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to accept job or job already taken. Affected rows: ' . $stmt->affected_rows]);
}

$stmt->close();
$conn->close();
?>
