<?php
session_start();

// Function to check if user is logged in
function checkUserSession() {
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    return true;
}

// Function to get current user ID
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Function to get user role
function getUserRole() {
    return $_SESSION['role'] ?? null;
}

// Redirect to login if not authenticated
function requireLogin() {
    if (!checkUserSession()) {
        header('Location: ../index.html');
        exit();
    }
}
?>
