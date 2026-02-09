<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT AVG(r.rating) AS average_rating FROM ratings r
        JOIN jobs j ON r.job_id = j.job_id
        WHERE j.provider_id = (SELECT provider_id FROM service_providers WHERE user_id = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$average_rating = 0;
if ($row = $result->fetch_assoc()) {
    $average_rating = round($row['average_rating'] ?? 0, 1);
}

header('Content-Type: application/json');
echo json_encode(['average_rating' => $average_rating]);

$stmt->close();
$conn->close();
?>
