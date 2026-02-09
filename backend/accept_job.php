<?php
session_start();
include 'db_connect.php';

$log_file = 'accept_job.log';

$log_message = "------------------\n";
$log_message .= "Time: " . date('Y-m-d H:i:s') . "\n";

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    $log_message .= "Error: Unauthorized - user_id not in session\n";
    file_put_contents($log_file, $log_message, FILE_APPEND);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$provider_id = $_SESSION['user_id'];
$log_message .= "Provider ID from session: " . $provider_id . "\n";

$data = json_decode(file_get_contents('php://input'), true);
$request_id = $data['request_id'];
$log_message .= "Request ID from POST: " . $request_id . "\n";

$sql = "UPDATE service_requests SET provider_id = ?, status = 'accepted' WHERE request_id = ? AND provider_id IS NULL";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $provider_id, $request_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        // Insert into job_status_updates
        $sql_insert = "INSERT INTO job_status_updates (request_id, status, message, update_time) VALUES (?, 'accepted', 'Job accepted by provider', NOW())";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("i", $request_id);
        $stmt_insert->execute();
        $stmt_insert->close();

        $log_message .= "Success: Job accepted.\n";
        file_put_contents($log_file, $log_message, FILE_APPEND);
        echo json_encode(['success' => true]);
    } else {
        http_response_code(409);
        $log_message .= "Error: Job already taken or does not exist. Affected rows: " . $stmt->affected_rows . "\n";
        file_put_contents($log_file, $log_message, FILE_APPEND);
        echo json_encode(['error' => 'Job already taken or does not exist']);
    }
} else {
    http_response_code(500);
    $log_message .= "Error: Failed to execute query: " . $stmt->error . "\n";
    file_put_contents($log_file, $log_message, FILE_APPEND);
    echo json_encode(['error' => 'Failed to accept job']);
}

$stmt->close();
$conn->close();
?>
