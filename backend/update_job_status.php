<?php
session_start();
include 'db_connect.php';

error_log("update_job_status.php called");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'service_provider') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$provider_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['status']) || !isset($data['job_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing status or job_id']);
    exit;
}

$status = $data['status'];
$request_id = $data['job_id'];
$message = $data['message'] ?? '';

// Valid statuses
$valid_statuses = ['accepted', 'en_route', 'arrived', 'working', 'completed'];

if (!in_array($status, $valid_statuses)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid status: ' . $status]);
    exit;
}

$sql_update = "UPDATE service_requests SET status = ?";
if ($status == 'en_route') {
    $sql_update .= ", started_time = NOW()";
} else if ($status == 'arrived') {
    $sql_update .= ", arrived_time = NOW()";
} else if ($status == 'working') {
    $sql_update .= ", in_progress_time = NOW()";
} else if ($status == 'completed') {
    $sql_update .= ", completed_time = NOW()";
}
$sql_update .= " WHERE request_id = ? AND provider_id = ?";

$stmt = $conn->prepare($sql_update);
if (!$stmt) {
    error_log("SQL prepare error: " . $conn->error);
    http_response_code(500);
    echo json_encode(['error' => 'Database prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("sii", $status, $request_id, $provider_id);

if ($stmt->execute()) {
    // Insert into job_status_updates
    $sql_insert = "INSERT INTO job_status_updates (request_id, status, message, update_time) VALUES (?, ?, ?, NOW())";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("iss", $request_id, $status, $message);
    $stmt_insert->execute();
    $stmt_insert->close();

    if ($status == 'completed') {
        // Insert into service_history
        $sql_history = "INSERT INTO service_history (request_id, completion_time, cost, feedback, rating) VALUES (?, NOW(), 0, NULL, NULL)";
        $stmt_history = $conn->prepare($sql_history);
        $stmt_history->bind_param("i", $request_id);
        $stmt_history->execute();
        $stmt_history->close();
    }

    error_log("Status updated successfully: " . $status . " for request: " . $request_id);
    echo json_encode(['success' => true, 'message' => 'Status updated to ' . $status]);
} else {
    error_log("Status update failed: " . $stmt->error);
    echo json_encode(['success' => false, 'error' => 'Failed to update status: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
