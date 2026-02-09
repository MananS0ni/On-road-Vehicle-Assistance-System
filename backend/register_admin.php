<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $verification_code = $_POST['verificationCode'];
    if ($verification_code !== 'ADMIN2025') {
        echo "Invalid verification code!";
        exit();
    }

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO admins (name, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $name, $email, $password);

    if ($stmt->execute()) {
        header("Location: ../frontend/dashboard-admin.html");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>
