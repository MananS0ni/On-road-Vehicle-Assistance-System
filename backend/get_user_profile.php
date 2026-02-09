<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT full_name AS name, email, phone FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$profile = null;
if ($row = $result->fetch_assoc()) {
    $profile = $row;
}

header('Content-Type: application/json');
echo json_encode(['profile' => $profile]);

$stmt->close();
$conn->close();
?>
