<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone_number = $_POST['phone'];
    $vehicle_type = $_POST['vehicleType'];
    $vehicle_brand = $_POST['vehicleBrand'];
    $vehicle_model = $_POST['vehicleModel'];
    $number_plate = $_POST['vehicleReg'];
    $fuel_type = 'Petrol'; // Default

    $sql_owner = "INSERT INTO vehicle_owners (name, email, password, phone_number) VALUES (?, ?, ?, ?)";
    $stmt_owner = $conn->prepare($sql_owner);
    $stmt_owner->bind_param("ssss", $name, $email, $password, $phone_number);

    if ($stmt_owner->execute()) {
        $owner_id = $conn->insert_id;
        $sql_vehicle = "INSERT INTO vehicles (owner_id, vehicle_type, vehicle_brand, vehicle_model, number_plate, fuel_type) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_vehicle = $conn->prepare($sql_vehicle);
        $stmt_vehicle->bind_param("isssss", $owner_id, $vehicle_type, $vehicle_brand, $vehicle_model, $number_plate, $fuel_type);

        if ($stmt_vehicle->execute()) {
            header("Location: ../frontend/dashboard-vehicle-owner.html");
            exit();
        } else {
            echo "Error: " . $sql_vehicle . "<br>" . $conn->error;
        }
        $stmt_vehicle->close();
    } else {
        echo "Error: " . $sql_owner . "<br>" . $conn->error;
    }
    $stmt_owner->close();

    $stmt->close();
}

$conn->close();
?>
