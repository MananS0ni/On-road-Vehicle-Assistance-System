<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];

$data = json_decode(file_get_contents('php://input'), true);
$availability = $data['availability'] ? 1 : 0;

$sql = "UPDATE service_providers SET availability = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $availability, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to update availability']);
}

$stmt->close();
$conn->close();
?>
