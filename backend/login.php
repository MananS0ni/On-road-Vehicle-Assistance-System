<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check vehicle_owners first
    $sql = "SELECT * FROM vehicle_owners WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = 'vehicle_owner';
            $_SESSION['user_name'] = $row['name']; // Add this for debugging
            header("Location: ../frontend/dashboard-vehicle-owner.html");
            exit();
        }
    }

    // Check service_providers
    $sql = "SELECT * FROM service_providers WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = 'service_provider';
            $_SESSION['user_name'] = $row['name']; // Add this for debugging
            header("Location: ../frontend/dashboard-service-provider.html");
            exit();
        }
    }

    // Check admins
    $sql = "SELECT * FROM admins WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = 'admin';
            $_SESSION['user_name'] = $row['name']; // Add this for debugging
            header("Location: ../frontend/dashboard-admin.php");
            exit();
        }
    }

    // Invalid credentials
    echo "Invalid email or password!";
}

$conn->close();
?>
