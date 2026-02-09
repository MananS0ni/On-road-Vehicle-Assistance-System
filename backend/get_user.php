<?php
session_start();
include 'db_connect.php';
header('Content-Type: application/json');

if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'];
    $table_name = '';

    switch ($role) {
        case 'vehicle_owner':
            $table_name = 'vehicle_owners';
            break;
        case 'service_provider':
            $table_name = 'service_providers';
            break;
        case 'admin':
            $table_name = 'admins';
            break;
    }

    if ($table_name) {
        $sql = "SELECT name FROM $table_name WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo json_encode(['user_name' => $row['name']]);
        } else {
            echo json_encode(['user_name' => 'Guest']);
        }
        $stmt->close();
    } else {
        echo json_encode(['user_name' => 'Guest']);
    }
} else {
    echo json_encode(['user_name' => 'Guest']);
}

$conn->close();
?>
