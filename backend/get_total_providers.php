<?php
include 'db_connect.php';

$sql = "SELECT COUNT(*) AS total_providers FROM service_providers";
$result = $conn->query($sql);

$total_providers = 0;
if ($result && $row = $result->fetch_assoc()) {
    $total_providers = $row['total_providers'];
}

header('Content-Type: application/json');
echo json_encode(['total_providers' => $total_providers]);

$conn->close();
?>
