<?php
session_start();
include 'db_connect.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'service_provider') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
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

// Update the request status to 'declined'
$sql = "UPDATE service_requests SET status = 'declined' WHERE request_id = ? AND status = 'pending'";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Database prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $request_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Job declined successfully']);
    } else {
        echo json_encode(['success' => false, 'error' => 'No pending job found with this ID']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to decline job: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
