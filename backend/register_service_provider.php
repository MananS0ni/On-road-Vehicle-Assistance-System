<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $business_name = $_POST['businessName'];
    $business_phone = $_POST['phone'];
    $workshop_address = $_POST['serviceArea'];
    $experience = $_POST['experience'];
    $service_radius = 10; // Default
    $services_offered = $_POST['serviceType'];

    $sql = "INSERT INTO service_providers (name, email, password, business_name, business_phone, workshop_address, experience, service_radius, services_offered) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssiss", $name, $email, $password, $business_name, $business_phone, $workshop_address, $experience, $service_radius, $services_offered);

    if ($stmt->execute()) {
        header("Location: ../frontend/dashboard-service-provider.html");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>
