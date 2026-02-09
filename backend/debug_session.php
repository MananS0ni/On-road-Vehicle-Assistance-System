<?php
session_start();
echo "<h2>Debug Session Info</h2>";
echo "<pre>";
echo "Session ID: " . session_id() . "\n";
echo "User ID: " . ($_SESSION['user_id'] ?? 'NOT SET') . "\n";
echo "Role: " . ($_SESSION['role'] ?? 'NOT SET') . "\n";
echo "Full Session: ";
print_r($_SESSION);
echo "</pre>";

// Test database connection
include 'db_connect.php';
if ($conn) {
    echo "<p>✅ Database connected</p>";
    
    // Check if user exists as service provider
    if (isset($_SESSION['user_id'])) {
        $sql = "SELECT name FROM service_providers WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            echo "<p>✅ Service provider found in database</p>";
        } else {
            echo "<p>❌ Service provider NOT found in database</p>";
        }
    }
} else {
    echo "<p>❌ Database connection failed</p>";
}
?>
