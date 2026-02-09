<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$sql = "SELECT id, name, email, phone_number FROM vehicle_owners ORDER BY id DESC";
$result = $conn->query($sql);

$users = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

echo json_encode(['users' => $users]);

$conn->close();
?>
