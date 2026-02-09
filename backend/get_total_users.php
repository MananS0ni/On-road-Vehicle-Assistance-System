<?php
include 'db_connect.php';

$sql = "SELECT COUNT(*) AS total_users FROM vehicle_owners";
$result = $conn->query($sql);

if (!$result) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $conn->error]);
    $conn->close();
    exit;
}

$total_users = 0;
if ($row = $result->fetch_assoc()) {
    $total_users = $row['total_users'];
}

header('Content-Type: application/json');
echo json_encode(['total_users' => $total_users]);

$conn->close();
?>
