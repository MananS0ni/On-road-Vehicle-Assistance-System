<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$sql = "SELECT id, name, email, business_phone, business_name, rating, availability FROM service_providers ORDER BY id DESC";
$result = $conn->query($sql);

$providers = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $providers[] = $row;
    }
}

echo json_encode(['providers' => $providers]);

$conn->close();
?>
