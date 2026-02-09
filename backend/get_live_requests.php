<?php
include 'db_connect.php';

$sql = "SELECT sr.request_id, vo.name AS user_name, sp.name AS provider_name, sr.service_type, sr.status, sr.request_time, sr.location
        FROM service_requests sr
        JOIN vehicles v ON sr.vehicle_id = v.vehicle_id
        JOIN vehicle_owners vo ON v.owner_id = vo.id
        LEFT JOIN service_providers sp ON sr.provider_id = sp.id
        WHERE sr.status IN ('pending', 'accepted', 'en_route', 'arrived', 'in_progress')
        ORDER BY sr.request_time DESC";
$result = $conn->query($sql);

$requests = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $requests[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode(['requests' => $requests]);

$conn->close();
?>
