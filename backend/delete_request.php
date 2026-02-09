<?php
session_start();
include 'db_connect.php';

error_log("delete_request.php called");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized - Admin access required']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['request_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing request_id']);
    exit;
}

$request_id = $data['request_id'];

// Delete the service request (cascades to related tables via foreign keys)
$sql = "DELETE FROM service_requests WHERE request_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("SQL prepare error: " . $conn->error);
    http_response_code(500);
    echo json_encode(['error' => 'Database prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $request_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        error_log("Request deleted successfully: " . $request_id);
        echo json_encode(['success' => true, 'message' => 'Request deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'error' => 'No request found with that ID']);
    }
} else {
    error_log("Delete failed: " . $stmt->error);
    echo json_encode(['success' => false, 'error' => 'Failed to delete request: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
