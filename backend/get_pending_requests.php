<?php
session_start();
include 'db_connect.php';

// Get all pending service requests that are not assigned to any provider AND not declined
$sql = "SELECT sr.*, v.vehicle_brand, v.vehicle_model, v.number_plate, 
               vo.name as customer_name, vo.phone_number
        FROM service_requests sr
        JOIN vehicles v ON sr.vehicle_id = v.vehicle_id
        JOIN vehicle_owners vo ON v.owner_id = vo.id
        WHERE sr.status = 'pending' AND (sr.provider_id IS NULL OR sr.provider_id = 0)
        ORDER BY sr.request_time DESC";

$result = $conn->query($sql);

$requests = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $requests[] = $row;
    }
}

echo json_encode($requests);
$conn->close();
?>
