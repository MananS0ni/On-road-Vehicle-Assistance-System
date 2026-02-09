<?php
include 'db_connect.php';

$sql = "SELECT v.verification_id, u.full_name, v.document_type, v.status, v.submitted_at
        FROM verifications v
        JOIN users u ON v.user_id = u.user_id
        WHERE v.status = 'pending'
        ORDER BY v.submitted_at DESC";
$result = $conn->query($sql);

$pending_verifications = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $pending_verifications[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode(['pending_verifications' => $pending_verifications]);

$conn->close();
?>
