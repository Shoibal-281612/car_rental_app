<?php
// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if logged in user is a customer
function isCustomer() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'customer';
}

// Check if logged in user is an agency
function isAgency() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'agency';
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . 'login.php');
        exit;
    }
}

// Redirect if not agency
function requireAgency() {
    requireLogin();
    if (!isAgency()) {
        header('Location: ' . BASE_URL . 'cars.php');
        exit;
    }
}

// Redirect if not customer
function requireCustomer() {
    requireLogin();
    if (!isCustomer()) {
        header('Location: ' . BASE_URL . 'cars.php');
        exit;
    }
}

// Sanitize input (simple)
function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($data)));
}
?>